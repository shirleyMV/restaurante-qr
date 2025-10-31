<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pagos MODIFY COLUMN estado ENUM('pendiente', 'completado', 'cancelado') NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pagos MODIFY COLUMN estado ENUM('pendiente', 'completado', 'fallido') NOT NULL DEFAULT 'pendiente'");
    }
};