<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador si no existe
        if (!User::where('email', 'admin@restaurante.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@restaurante.com',
                'password' => bcrypt('Admin123!'),
            ]);
            echo "Usuario admin creado exitosamente\n";
        } else {
            echo "Usuario admin ya existe\n";
        }

        // Crear categorías
        $platos = Categoria::create([
            'nombre' => 'Platos Principales',
            'descripcion' => 'Comida casera y típica',
            'activo' => true
        ]);

        $bebidas = Categoria::create([
            'nombre' => 'Bebidas',
            'descripcion' => 'Bebidas frías',
            'activo' => true
        ]);

        // Lista de productos (tus productos reales)
        $productos = [
            // Platos
            ['nombre' => 'Locro sencillo', 'precio' => 3, 'categoria_id' => $platos->id, 'imagen' => 'productos/locro-sencillo.jpg'],
            ['nombre' => 'Locro con presa', 'precio' => 5, 'categoria_id' => $platos->id, 'imagen' => 'productos/Locro_presa.jpg'],
            ['nombre' => 'Guiso de fideo', 'precio' => 8, 'categoria_id' => $platos->id, 'imagen' => 'productos/guiso.jpg'],
            ['nombre' => 'Salchipollo', 'precio' => 15, 'categoria_id' => $platos->id, 'imagen' => 'productos/salchipollo.jpg'],
            ['nombre' => 'Salchipapa', 'precio' => 10, 'categoria_id' => $platos->id, 'imagen' => 'productos/salchipapa.jpg'],
            ['nombre' => 'Pollo frito', 'precio' => 8, 'categoria_id' => $platos->id, 'imagen' => 'productos/pollo_frito.jpg'],
            ['nombre' => 'Picante de pollo', 'precio' => 10, 'categoria_id' => $platos->id, 'imagen' => 'productos/picante_pollo.jpg'],
            ['nombre' => 'Silpancho', 'precio' => 10, 'categoria_id' => $platos->id, 'imagen' => 'productos/silpancho.jpg'],
            
            // Bebidas
            ['nombre' => 'Coca-Cola 500ml', 'precio' => 2.50, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/coca-cola-500ml.jpg'],
            ['nombre' => 'Coca-Cola 2L', 'precio' => 11, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/coca-cola-2L.jpg'],
            ['nombre' => 'Sprite 500ml', 'precio' => 2.50, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/sprite-500ml.jpg'],
            ['nombre' => 'Sprite 2L', 'precio' => 11, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/sprite-2L.jpg'],
            ['nombre' => 'Fanta 500ml', 'precio' => 2.50, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/fanta-500ml.jpg'],
            ['nombre' => 'Fanta 2L', 'precio' => 11, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/fanta-2L.jpg'],
            ['nombre' => 'Pepsi 500ml', 'precio' => 2.50, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/pepsi-500ml.jpg'],
            ['nombre' => 'Pepsi 2L', 'precio' => 11, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/pepsi-2L.jpg'],
            ['nombre' => '7UP 500ml', 'precio' => 2.50, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/7up-500ml.jpg'],
            ['nombre' => '7UP 2L', 'precio' => 11, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/7up-2L.jpg'],
            ['nombre' => 'Simba 500ml', 'precio' => 2.50, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/simba-500ml.jpg'],
            ['nombre' => 'Simba 2L', 'precio' => 10, 'categoria_id' => $bebidas->id, 'imagen' => 'productos/simba-2L.jpg'],
        ];

        foreach ($productos as $prod) {
            Producto::create([
                'categoria_id' => $prod['categoria_id'],
                'nombre' => $prod['nombre'],
                'descripcion' => '',
                'precio' => $prod['precio'],
                'imagen' => $prod['imagen'],
                'disponible' => true
            ]);
        }

        // Crear mesas
        for ($i = 1; $i <= 10; $i++) {
            $mesa = Mesa::create([
                'numero' => (string)$i,
                'capacidad' => rand(2, 6),
                'estado' => 'disponible',
            ]);
            
            $mesa->generarCodigoQr();
        }

        // Crear métodos de pago
        $metodos = [
            ['nombre' => 'Efectivo'],
            ['nombre' => 'QR'],
        ];

        foreach ($metodos as $metodo) {
            MetodoPago::create($metodo);
        }
    }
}