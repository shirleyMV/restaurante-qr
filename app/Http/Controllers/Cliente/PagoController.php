<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Pago;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PagoController extends Controller
{
    // Mostrar opciones de pago
    public function mostrar($pedido_id)
    {
        $pedido = Pedido::with('detalles.producto', 'mesa')->findOrFail($pedido_id);
        $metodosPago = MetodoPago::where('activo', true)->get();

        return view('cliente.pago', compact('pedido', 'metodosPago'));
    }

// Generar QR para pago usando BNB
public function generarQrPago(Request $request)
{
    $request->validate([
        'pedido_id' => 'required|exists:pedidos,id',
        'metodo_pago_id' => 'required|exists:metodos_pago,id',
    ]);

    $pedido = Pedido::findOrFail($request->pedido_id);
    $metodoPago = MetodoPago::findOrFail($request->metodo_pago_id);

    // Generar código de transacción único
    $codigoTransaccion = 'TXN-' . $pedido->id . '-' . time();

    // Crear el registro de pago
    $pago = Pago::create([
        'pedido_id' => $pedido->id,
        'metodo_pago_id' => $metodoPago->id,
        'monto' => $pedido->total,
        'codigo_transaccion' => $codigoTransaccion,
        'estado' => 'pendiente',
    ]);

    // Preparar glosa para BNB
    $glosa = 'Doña Julia - Mesa ' . $pedido->mesa->numero . ' - Pedido #' . $pedido->id;

    // Usar el servicio BNB
    $bnbService = new \App\Services\BnbQrService();
    $resultado = $bnbService->generarQrSimple($pedido->total, $glosa);

    if ($resultado['success']) {
        return response()->json([
            'success' => true,
            'qr_code' => $resultado['qr_image'],
            'qr_type' => 'svg',
            'codigo_transaccion' => $codigoTransaccion,
            'monto' => $pedido->total,
            'simulado' => $resultado['simulado'] ?? false,
            'message' => $resultado['message'],
            'comprobante_url' => route('comprobante.show', $pedido->id),
            'pedido_id' => $pedido->id,
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Error al generar QR de pago',
    ], 500);
}
    // Confirmar pago (simulado)
    public function confirmarPago(Request $request)
    {
        $request->validate([
            'codigo_transaccion' => 'required|exists:pagos,codigo_transaccion',
        ]);

        $pago = Pago::where('codigo_transaccion', $request->codigo_transaccion)->firstOrFail();
        $pago->estado = 'completado';
        $pago->save();

        $pedido = $pago->pedido;
        $pedido->estado = 'entregado';
        $pedido->save();

        // Liberar la mesa
        $pedido->mesa->estado = 'disponible';
        $pedido->mesa->save();

        return response()->json([
            'success' => true,
            'message' => '¡Pago realizado con éxito! Gracias por su visita.',
        ]);
    }


    // Pago en efectivo
public function pagoEfectivo(Request $request)
{
    $request->validate([
        'pedido_id' => 'required|exists:pedidos,id',
        'metodo_pago_id' => 'required|exists:metodos_pago,id',
    ]);

    $pedido = Pedido::findOrFail($request->pedido_id);
    $metodoPago = MetodoPago::findOrFail($request->metodo_pago_id);

    // Crear registro de pago pendiente
    Pago::create([
        'pedido_id' => $pedido->id,
        'metodo_pago_id' => $metodoPago->id,
        'monto' => $pedido->total,
        'codigo_transaccion' => 'EFECTIVO-' . $pedido->id . '-' . time(),
        'estado' => 'pendiente', // La cajera lo confirmará
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Pago registrado. Por favor acércate a caja para pagar.',
        'comprobante_url' => route('comprobante.show', $pedido->id),
    ]);
}
}