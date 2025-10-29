<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pedido::query()
                    ->with(['mesa', 'cliente'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('mesa.numero')
                    ->label('Mesa')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('BOB')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_preparacion',
                        'info' => 'listo',
                        'success' => 'entregado',
                        'danger' => 'cancelado',
                    ]),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }
}