<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
    'user_id',
    'total',
    'estado',
    'transaccion_id',
    'estado_pago',
    'fecha_pago'
    ];

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}