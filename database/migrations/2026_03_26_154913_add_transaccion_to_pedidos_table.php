<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('transaccion_id')->nullable()->after('estado');
            $table->string('estado_pago')->default('pendiente')->after('transaccion_id');
            $table->timestamp('fecha_pago')->nullable()->after('estado_pago');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['transaccion_id', 'estado_pago', 'fecha_pago']);
        });
    }
};