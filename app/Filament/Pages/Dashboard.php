<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Panel de Control';
    
    protected static ?string $title = 'Panel de Control';
    
    protected static string $routePath = 'panel-de-control';
    
    public function getHeading(): string
    {
        return 'Panel de Control';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\PedidosPendientesWidget::class,
            \App\Filament\Widgets\LatestOrders::class,
            \App\Filament\Widgets\TopProducts::class,
        ];
    }
}
