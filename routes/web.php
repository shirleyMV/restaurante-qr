<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cliente\MenuController;
use App\Http\Controllers\Cliente\PedidoController as ClientePedidoController;
use App\Http\Controllers\Cliente\PagoController;

// Ruta principal
Route::get('/', function () {
    return view('welcome');
});

// Ruta temporal para crear usuario admin
Route::get('/crear-admin-temporal', function () {
    if (\App\Models\User::where('email', 'admin@restaurante.com')->exists()) {
        return 'Usuario admin ya existe';
    }
    
    \App\Models\User::create([
        'name' => 'Admin',
        'email' => 'admin@restaurante.com',
        'password' => bcrypt('Admin123!'),
    ]);
    
    return 'Usuario admin creado exitosamente. Ahora puedes acceder a /admin con email: admin@restaurante.com y password: Admin123!';
});

// Rutas del Cliente (escanear QR y hacer pedidos)
Route::prefix('cliente')->name('cliente.')->group(function () {
    // Ver menú escaneando QR
    Route::get('/menu/{codigo_qr}', [MenuController::class, 'mostrar'])->name('menu');
    
    // Hacer pedido
    Route::post('/pedido', [ClientePedidoController::class, 'store'])->name('pedido.store');
    Route::get('/pedido/{mesa_id}', [ClientePedidoController::class, 'verPedido'])->name('pedido.ver');
    
    // Pagar
    Route::get('/pago/{pedido_id}', [PagoController::class, 'mostrar'])->name('pago.mostrar');
    Route::post('/pago/generar-qr', [PagoController::class, 'generarQrPago'])->name('pago.generar-qr');
    Route::post('/pago/confirmar', [PagoController::class, 'confirmarPago'])->name('pago.confirmar');
Route::post('/pago/efectivo', [PagoController::class, 'pagoEfectivo'])->name('pago.efectivo');

});

// API para productos por categoría
Route::get('/api/productos/categoria/{categoria_id}', [MenuController::class, 'productosPorCategoria']);