<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Pago;
use App\Events\ComprobanteSubido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComprobanteController extends Controller
{
    public function show($pedidoId)
    {
        $pedido = Pedido::with(['mesa', 'cliente', 'detalles.producto', 'pagos'])->findOrFail($pedidoId);
        
        return view('comprobante.upload', compact('pedido'));
    }

    public function upload(Request $request, $pedidoId)
    {
        $request->validate([
            'comprobante' => 'required|image|max:5120', // 5MB max
        ]);

        $pedido = Pedido::findOrFail($pedidoId);
        
        // Buscar o crear el pago
        $pago = Pago::where('pedido_id', $pedidoId)->first();
        
        if (!$pago) {
            return back()->with('error', 'No se encontró el pago asociado a este pedido.');
        }

        // Guardar la imagen
        if ($request->hasFile('comprobante')) {
            // Eliminar comprobante anterior si existe
            if ($pago->comprobante) {
                Storage::disk('public')->delete($pago->comprobante);
            }
            
            $path = $request->file('comprobante')->store('comprobantes', 'public');
            $pago->comprobante = $path;
            $pago->save();
            
            // Disparar evento para notificar a la cajera
            event(new ComprobanteSubido($pago));
        }

        return response()->json(['success' => true, 'message' => '¡Comprobante enviado correctamente!']);
    }
}