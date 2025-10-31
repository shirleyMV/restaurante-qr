<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Cliente;
use App\Events\PedidoCancelado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    // Crear un nuevo pedido
    public function store(Request $request)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'cliente.nombre' => 'required|string|max:255',
            'cliente.telefono' => 'nullable|string|max:20',
            'cliente.correo' => 'nullable|email|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Validar stock disponible ANTES de crear el pedido
            $productosInsuficientes = [];
            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['id']);
                
                if (!$producto->tieneStock($item['cantidad'])) {
                    $productosInsuficientes[] = [
                        'nombre' => $producto->nombre,
                        'solicitado' => $item['cantidad'],
                        'disponible' => $producto->stock,
                    ];
                }
            }

            // Si hay productos sin stock suficiente, retornar error
            if (!empty($productosInsuficientes)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente para algunos productos',
                    'productos_insuficientes' => $productosInsuficientes,
                ], 400);
            }

            // Crear o encontrar el cliente
            $clienteData = $request->input('cliente');
            $cliente = Cliente::firstOrCreate(
                ['nombre' => $clienteData['nombre']],
                [
                    'telefono' => $clienteData['telefono'] ?? null,
                    'correo' => $clienteData['correo'] ?? null,
                ]
            );

            // Crear el pedido
            $pedido = Pedido::create([
                'mesa_id' => $request->mesa_id,
                'cliente_id' => $cliente->id,
                'estado' => 'pendiente',
            ]);

            // Agregar los detalles del pedido y descontar stock
            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['id']);
                
                // Crear detalle del pedido
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                ]);

                // Descontar stock
                $producto->descontarStock($item['cantidad']);
            }

            // Calcular el total
            $pedido->calcularTotal();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '¡Pedido realizado con éxito!',
                'pedido' => $pedido->load('detalles.producto'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Ver el pedido actual de una mesa
    public function verPedido($mesa_id)
    {
        $pedido = Pedido::where('mesa_id', $mesa_id)
            ->whereIn('estado', ['pendiente', 'en_preparacion'])
            ->with('detalles.producto')
            ->latest()
            ->first();

        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'No hay pedido activo',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'pedido' => $pedido,
        ]);
    }
    
    // Cancelar pedido
    public function cancelar(Request $request, Pedido $pedido)
    {
        $request->validate([
            'motivo' => 'required|string|max:255',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Verificar que el pedido no esté confirmado
            if ($pedido->estado === 'confirmado' || $pedido->estado === 'en_preparacion' || $pedido->estado === 'listo') {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes cancelar un pedido que ya está en preparación. Contacta a la cajera.',
                ], 400);
            }
            
            // Devolver stock de los productos
            foreach ($pedido->detalles as $detalle) {
                $producto = $detalle->producto;
                $producto->stock += $detalle->cantidad;
                $producto->save();
            }
            
            // Actualizar pedido
            $pedido->estado = 'cancelado';
            $pedido->motivo_cancelacion = $request->motivo;
            $pedido->fecha_cancelacion = now();
            $pedido->save();
            
            // Cancelar pagos asociados
            foreach ($pedido->pagos as $pago) {
                $pago->estado = 'cancelado';
                $pago->motivo_cancelacion = $request->motivo;
                $pago->fecha_cancelacion = now();
                $pago->save();
            }
            
            // Disparar evento
            event(new PedidoCancelado($pedido));
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pedido cancelado exitosamente',
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar el pedido: ' . $e->getMessage(),
            ], 500);
        }
    }
}