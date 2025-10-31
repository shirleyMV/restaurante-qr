<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreignId('confirmado_por')->nullable()->after('estado')->constrained('users');
            $table->timestamp('fecha_confirmacion')->nullable()->after('confirmado_por');
            $table->text('notas')->nullable()->after('fecha_confirmacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['confirmado_por']);
            $table->dropColumn(['confirmado_por', 'fecha_confirmacion', 'notas']);
        });
    }
};
