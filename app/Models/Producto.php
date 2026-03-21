<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion', 
        'precio',
        'existencia',
        'imagen1',
        'imagen2',
        'imagen3'
    ];
}