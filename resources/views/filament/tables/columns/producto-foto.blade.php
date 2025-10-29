@php
    $path = $record?->imagen;
    $relative = $path ? '/storage/' . ltrim($path, '/') : null;
    $version = null;
    if ($path) {
        $local = storage_path('app/public/' . ltrim($path, '/'));
        if (is_file($local)) {
            $version = filemtime($local);
        }
    }
@endphp

@if ($relative)
    <img src="{{ $relative }}{{ $version ? ('?v='.$version) : '' }}" alt="Foto" style="width:60px;height:60px;border-radius:50%;object-fit:cover;" loading="lazy" onerror="this.style.display='none'">
@else
    <x-filament::icon icon="heroicon-o-photo" class="h-10 w-10 text-gray-400" />
@endif
