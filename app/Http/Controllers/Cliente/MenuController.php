<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // Mostrar el menú cuando se escanea el QR
    public function mostrar($codigo_qr)
    {
        $mesa = Mesa::where('codigo_qr', $codigo_qr)->firstOrFail();
        
        // Cambiar estado de la mesa a ocupada
        if ($mesa->estado == 'disponible') {
            $mesa->estado = 'ocupada';
            $mesa->save();
        }

        $categorias = Categoria::where('activo', true)
            ->with(['productos' => function($query) {
                $query->where('disponible', true);
            }])
            ->get();

        return view('cliente.menu', compact('mesa', 'categorias'));
    }

    // API para obtener productos por categoría
    public function productosPorCategoria($categoria_id)
    {
        $productos = Producto::where('categoria_id', $categoria_id)
            ->where('disponible', true)
            ->get();

        return response()->json($productos);
    }
}