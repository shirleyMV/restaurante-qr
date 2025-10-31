<?php

namespace App\Filament\Resources\PagoResource\Pages;

use App\Filament\Resources\PagoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPagos extends ListRecords
{
    protected static string $resource = PagoResource::class;
    
    // Actualizar cada 5 segundos para mostrar nuevos comprobantes
    protected static ?string $pollingInterval = '5s';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    // Script para escuchar eventos en tiempo real
    protected function getHeaderWidgets(): array
    {
        return [];
    }
    
    public function getFooterWidgetsColumns(): int | array
    {
        return 1;
    }
}
