<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Data\ProductosData;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = ProductosData::todos();
        return response()->json($productos);
    }

    public function show($id)
    {
        $producto = ProductosData::porId($id);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto);
    }
}