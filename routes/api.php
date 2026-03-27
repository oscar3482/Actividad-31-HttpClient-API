<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\PagoController;

// Rutas públicas
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);

// Autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Callbacks de PayPal (públicas, sin token)
Route::get('/pagos/{id}/ejecutar', [PagoController::class, 'ejecutarPago']);
Route::get('/pagos/{id}/cancelar', [PagoController::class, 'cancelarPago']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Perfil
    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::put('/perfil', [AuthController::class, 'actualizarPerfil']);
    Route::post('/perfil/imagen', [AuthController::class, 'actualizarImagen']);
    Route::put('/perfil/password', [AuthController::class, 'actualizarPassword']);

    // Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index']);
    Route::post('/pedidos', [PedidoController::class, 'store']);
    Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
    Route::put('/pedidos/{id}/cancelar', [PedidoController::class, 'cancelar']);

    // Pagos
    Route::post('/pagos/{id}/iniciar', [PagoController::class, 'iniciarPago']);
});