<?php

namespace App\Filament\Widgets;

use App\Models\DetallePedido;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProducts extends ChartWidget
{
    protected static ?string $heading = 'Productos Más Vendidos';
    
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $topProducts = DetallePedido::select('producto_id', DB::raw('SUM(cantidad) as total'))
            ->with('producto')
            ->groupBy('producto_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad Vendida',
                    'data' => $topProducts->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $topProducts->pluck('producto.nombre')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}