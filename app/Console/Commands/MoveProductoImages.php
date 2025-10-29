<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Producto;

class MoveProductoImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:migrate-images {--dry-run : Only report actions without modifying files or DB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move existing product images from public/ into storage/app/public/productos and update DB paths';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        // Ensure target directory exists on public disk
        if (!$dryRun) {
            Storage::disk('public')->makeDirectory('productos');
        }

        $moved = 0;
        $skipped = 0;
        $updated = 0;
        $errors = 0;

        $this->info('Scanning productos...');

        Producto::query()->orderBy('id')->chunk(100, function ($chunk) use (&$moved, &$skipped, &$updated, &$errors, $dryRun) {
            foreach ($chunk as $producto) {
                $original = $producto->getRawOriginal('imagen');

                if (empty($original)) {
                    $skipped++;
                    $this->line("- Producto #{$producto->id}: sin imagen (omitido)");
                    continue;
                }

                // Normalizar el valor almacenado
                $path = ltrim($original, '/');

                // Si ya está en productos/ (disco public), solo aseguramos que exista y seguimos
                if (Str::startsWith($path, 'productos/')) {
                    if (Storage::disk('public')->exists($path)) {
                        $skipped++;
                        $this->line("- Producto #{$producto->id}: ya migrado => {$path}");
                        continue;
                    }
                    // Si no existe en el disco, intentaremos buscarlo en public root
                    $filename = basename($path);
                    $publicCandidate = public_path($filename);
                    if (is_file($publicCandidate)) {
                        if (!$dryRun) {
                            Storage::disk('public')->putFileAs('productos', $publicCandidate, $filename);
                        }
                        $moved++;
                        $this->line("- Producto #{$producto->id}: restaurado en public disk => productos/{$filename}");
                        continue;
                    }
                }

                // Si viene como 'storage/...' recortar a lo relativo del disco public
                if (Str::startsWith($path, 'storage/')) {
                    $relative = ltrim(Str::after($path, 'storage/'), '/');
                    if (Str::startsWith($relative, 'productos/')) {
                        // Solo actualizar el valor en BD si fuera necesario
                        if (!$dryRun) {
                            $producto->imagen = $relative;
                            $producto->save();
                        }
                        $updated++;
                        $this->line("- Producto #{$producto->id}: normalizado => {$relative}");
                        continue;
                    }
                    $path = $relative;
                }

                // Si el valor contiene subcarpetas distintas, intentaremos mover conservando el nombre
                $filename = basename($path);

                // Candidatos de origen en public/
                $candidates = [
                    public_path($filename),        // public/filename.jpg
                    public_path($path),            // public/<path>
                ];

                $source = null;
                foreach ($candidates as $cand) {
                    if (is_file($cand)) {
                        $source = $cand;
                        break;
                    }
                }

                if (!$source) {
                    // Como fallback, verificar si ya existe en el disco public bajo productos con ese nombre
                    $target = 'productos/' . $filename;
                    if (Storage::disk('public')->exists($target)) {
                        if (!$dryRun) {
                            $producto->imagen = $target;
                            $producto->save();
                        }
                        $updated++;
                        $this->line("- Producto #{$producto->id}: apuntado a existente => {$target}");
                        continue;
                    }

                    $errors++;
                    $this->warn("! Producto #{$producto->id}: archivo no encontrado para '{$original}'");
                    continue;
                }

                // Mover (copiar) al disco public/productos
                $target = 'productos/' . $filename;
                if (!$dryRun) {
                    // Si ya existe, no sobreescribir; si no, subirlo
                    if (!Storage::disk('public')->exists($target)) {
                        $stream = fopen($source, 'r');
                        Storage::disk('public')->put($target, $stream);
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }
                    // Actualizar DB para usar ruta relativa en el disco public
                    $producto->imagen = $target;
                    $producto->save();
                }

                $moved++;
                $updated++;
                $this->info("✓ Producto #{$producto->id}: migrado => {$target}");
            }
        });

        $this->newLine();
        $this->info("Resumen: movidos={$moved}, actualizados={$updated}, omitidos={$skipped}, errores={$errors}");

        if ($dryRun) {
            $this->comment('Dry-run completado. No se realizaron cambios.');
        }

        return self::SUCCESS;
    }
}
