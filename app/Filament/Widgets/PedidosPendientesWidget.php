<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PedidosPendientesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pedido::query()
                    ->where('activo', true)
                    ->whereIn('estado', ['pendiente', 'en_preparacion'])
                    ->with(['mesa', 'cliente', 'detalles.producto'])
                    ->latest()
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
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('detalles')
                    ->label('Productos')
                    ->formatStateUsing(function ($record) {
                        return $record->detalles->map(function ($detalle) {
                            return $detalle->cantidad . 'x ' . $detalle->producto->nombre;
                        })->join(', ');
                    })
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('BOB')
                    ->weight('bold')
                    ->color('success'),
                    
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_preparacion',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'en_preparacion' => 'En Preparación',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Hora')
                    ->dateTime('H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->heading('Pedidos Pendientes')
            ->description('Pedidos que están pendientes o en preparación');
    }
}