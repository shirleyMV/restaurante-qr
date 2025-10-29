<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\Cliente;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Pedidos de hoy y ayer
        $pedidosHoy = Pedido::whereDate('created_at', today())->count();
        $pedidosAyer = Pedido::whereDate('created_at', today()->subDay())->count();
        $difPedidos = $pedidosHoy - $pedidosAyer;
        $porcentajePedidos = $pedidosAyer > 0 ? (($difPedidos / $pedidosAyer) * 100) : 0;
        
        // Ventas de hoy y ayer (todos los pedidos, no solo entregados)
        $ventasHoy = Pedido::whereDate('created_at', today())
            ->whereNotIn('estado', ['cancelado'])
            ->sum('total');
        $ventasAyer = Pedido::whereDate('created_at', today()->subDay())
            ->whereNotIn('estado', ['cancelado'])
            ->sum('total');
        $difVentas = $ventasHoy - $ventasAyer;
        $porcentajeVentas = $ventasAyer > 0 ? (($difVentas / $ventasAyer) * 100) : 0;
        
        // Mesas ocupadas
        $mesasOcupadas = Mesa::where('estado', 'ocupada')->count();
        $totalMesas = Mesa::count();
        $porcentajeOcupacion = $totalMesas > 0 ? (($mesasOcupadas / $totalMesas) * 100) : 0;
        
        // Clientes
        $totalClientes = Cliente::count();
        $clientesNuevosHoy = Cliente::whereDate('created_at', today())->count();

        return [
            Stat::make('Pedidos Hoy', $pedidosHoy)
                ->description(
                    $difPedidos >= 0 
                        ? "+{$difPedidos} desde ayer (" . number_format($porcentajePedidos, 1) . "%)" 
                        : "{$difPedidos} desde ayer (" . number_format($porcentajePedidos, 1) . "%)"
                )
                ->descriptionIcon($difPedidos >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($difPedidos >= 0 ? 'success' : 'danger')
                ->chart([7, 3, 4, 5, 6, 3, 5, $pedidosHoy]),
                
            Stat::make('Ventas Hoy', 'Bs ' . number_format($ventasHoy, 2))
                ->description(
                    $difVentas >= 0 
                        ? "+Bs " . number_format($difVentas, 2) . " (" . number_format($porcentajeVentas, 1) . "%)" 
                        : "Bs " . number_format($difVentas, 2) . " (" . number_format($porcentajeVentas, 1) . "%)"
                )
                ->descriptionIcon($difVentas >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($difVentas >= 0 ? 'success' : 'danger'),
                
            Stat::make('Mesas Ocupadas', $mesasOcupadas . ' / ' . $totalMesas)
                ->description(number_format($porcentajeOcupacion, 1) . '% de ocupación')
                ->descriptionIcon('heroicon-o-table-cells')
                ->color($porcentajeOcupacion > 70 ? 'danger' : ($porcentajeOcupacion > 40 ? 'warning' : 'success')),
                
            Stat::make('Clientes Registrados', $totalClientes)
                ->description($clientesNuevosHoy > 0 ? "+{$clientesNuevosHoy} nuevos hoy" : 'Total de clientes')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),
        ];
    }
}