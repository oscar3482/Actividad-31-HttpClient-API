<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    // Crear pedido
    public function store(Request $request)
    {
        $request->validate([
            'productos' => 'required|array',
            'productos.*.producto_id' => 'required|integer',
            'productos.*.nombre_producto' => 'required|string',
            'productos.*.precio' => 'required|numeric',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $total = 0;
        $detalles = [];

        foreach ($request->productos as $item) {
            $producto = Producto::find($item['producto_id']);

            if (!$producto || $producto->existencia < $item['cantidad']) {
                return response()->json([
                    'error' => "Stock insuficiente para: {$item['nombre_producto']}"
                ], 400);
            }

            $subtotal = $item['precio'] * $item['cantidad'];
            $total += $subtotal;

            $detalles[] = [
                'producto_id'      => $item['producto_id'],
                'nombre_producto'  => $item['nombre_producto'],
                'precio'           => $item['precio'],
                'cantidad'         => $item['cantidad'],
                'subtotal'         => $subtotal,
            ];

            // Descontar existencia
            $producto->decrement('existencia', $item['cantidad']);
        }

        $pedido = Pedido::create([
            'user_id' => $request->user()->id,
            'total'   => $total,
            'estado'  => 'pendiente',
        ]);

        foreach ($detalles as $detalle) {
            $detalle['pedido_id'] = $pedido->id;
            DetallePedido::create($detalle);
        }

        return response()->json([
            'message' => 'Pedido creado correctamente',
            'pedido'  => $pedido->load('detalles')
        ], 201);
    }

    // Listar pedidos del usuario
    public function index(Request $request)
    {
        $pedidos = Pedido::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pedidos);
    }

    // Detalle de un pedido
    public function show(Request $request, $id)
    {
        $pedido = Pedido::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('detalles')
            ->first();

        if (!$pedido) {
            return response()->json(['error' => 'Pedido no encontrado'], 404);
        }

        return response()->json($pedido);
    }

    // Cancelar pedido
    public function cancelar(Request $request, $id)
    {
        $pedido = Pedido::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$pedido) {
            return response()->json(['error' => 'Pedido no encontrado'], 404);
        }

        if ($pedido->estado === 'cancelado') {
            return response()->json(['error' => 'El pedido ya está cancelado'], 400);
        }

        // Restaurar existencia
        foreach ($pedido->detalles as $detalle) {
            $producto = Producto::find($detalle->producto_id);
            if ($producto) {
                $producto->increment('existencia', $detalle->cantidad);
            }
        }

        $pedido->update(['estado' => 'cancelado']);

        return response()->json(['message' => 'Pedido cancelado correctamente']);
    }
}