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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('motivo_cancelacion')->nullable()->after('estado');
            $table->unsignedBigInteger('cancelado_por')->nullable()->after('motivo_cancelacion');
            $table->timestamp('fecha_cancelacion')->nullable()->after('cancelado_por');
            
            $table->foreign('cancelado_por')->references('id')->on('users')->onDelete('set null');
        });
        
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('motivo_cancelacion')->nullable()->after('estado');
            $table->unsignedBigInteger('cancelado_por')->nullable()->after('motivo_cancelacion');
            $table->timestamp('fecha_cancelacion')->nullable()->after('cancelado_por');
            
            $table->foreign('cancelado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['cancelado_por']);
            $table->dropColumn(['motivo_cancelacion', 'cancelado_por', 'fecha_cancelacion']);
        });
        
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['cancelado_por']);
            $table->dropColumn(['motivo_cancelacion', 'cancelado_por', 'fecha_cancelacion']);
        });
    }
};
