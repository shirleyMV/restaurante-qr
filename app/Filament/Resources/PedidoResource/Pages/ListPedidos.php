<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use App\Models\Pedido;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListPedidos extends ListRecords
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
            Actions\Action::make('exportar_excel')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    return $this->exportarExcel();
                }),
                
            Actions\Action::make('reporte_ventas')
                ->label('Reporte de Ventas')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('fecha_inicio')
                        ->label('Fecha Inicio')
                        ->required()
                        ->default(now()->startOfMonth()),
                    \Filament\Forms\Components\DatePicker::make('fecha_fin')
                        ->label('Fecha Fin')
                        ->required()
                        ->default(now()),
                ])
                ->action(function (array $data) {
                    return $this->generarReporteVentas($data);
                }),
        ];
    }
    
    protected function exportarExcel()
    {
        $pedidos = Pedido::with(['mesa', 'cliente', 'detalles.producto'])
            ->latest()
            ->get();
        
        $csv = "ID,Mesa,Cliente,Teléfono,Total,Estado,Fecha\n";
        
        foreach ($pedidos as $pedido) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%.2f,%s,%s\n",
                $pedido->id,
                $pedido->mesa->numero ?? 'N/A',
                $pedido->cliente->nombre ?? 'Anónimo',
                $pedido->cliente->telefono ?? 'N/A',
                $pedido->total,
                $pedido->estado,
                $pedido->created_at->format('d/m/Y H:i')
            );
        }
        
        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'pedidos_' . now()->format('Y-m-d') . '.csv');
    }
    
    protected function generarReporteVentas(array $data)
    {
        $pedidos = Pedido::with(['mesa', 'cliente', 'detalles.producto'])
            ->whereBetween('created_at', [$data['fecha_inicio'], $data['fecha_fin']])
            ->get();
        
        $totalVentas = $pedidos->sum('total');
        $totalPedidos = $pedidos->count();
        $promedioVenta = $totalPedidos > 0 ? $totalVentas / $totalPedidos : 0;
        
        $csv = "REPORTE DE VENTAS\n";
        $csv .= "Período: {$data['fecha_inicio']} - {$data['fecha_fin']}\n\n";
        $csv .= "RESUMEN\n";
        $csv .= "Total Pedidos: {$totalPedidos}\n";
        $csv .= "Total Ventas: Bs {$totalVentas}\n";
        $csv .= "Promedio por Pedido: Bs " . number_format($promedioVenta, 2) . "\n\n";
        $csv .= "DETALLE DE PEDIDOS\n";
        $csv .= "ID,Mesa,Cliente,Teléfono,Total,Estado,Fecha\n";
        
        foreach ($pedidos as $pedido) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%.2f,%s,%s\n",
                $pedido->id,
                $pedido->mesa->numero ?? 'N/A',
                $pedido->cliente->nombre ?? 'Anónimo',
                $pedido->cliente->telefono ?? 'N/A',
                $pedido->total,
                $pedido->estado,
                $pedido->created_at->format('d/m/Y H:i')
            );
        }
        
        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'reporte_ventas_' . now()->format('Y-m-d') . '.csv');
    }
}
