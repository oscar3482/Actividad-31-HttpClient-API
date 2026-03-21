<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $fillable = [
        'pedido_id',
        'producto_id',
        'nombre_producto',
        'precio',
        'cantidad',
        'subtotal'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}