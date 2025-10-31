<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Cocina extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static string $view = 'filament.pages.cocina';
    
    protected static ?string $navigationLabel = 'Cocina';
    
    protected static ?string $title = 'Cocina';
    
    protected static ?string $navigationGroup = 'Operaciones';
    
    protected static ?int $navigationSort = 1;
    
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\PedidosCocinaWidget::class,
        ];
    }
}