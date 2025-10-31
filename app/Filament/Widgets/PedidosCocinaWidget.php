<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PedidosCocinaWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = '👨‍🍳 Pedidos para Cocina';
    
    // Actualizar cada 3 segundos para mostrar nuevos pedidos
    protected static ?string $pollingInterval = '3s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pedido::query()
                    ->whereHas('pagos', function ($query) {
                        $query->where('estado', 'completado')
                              ->whereNotNull('confirmado_por');
                    })
                    ->whereIn('estado', ['pendiente', 'en_preparacion'])
                    ->where('activo', true)
                    ->with(['mesa', 'cliente', 'detalles.producto', 'pagos'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Pedido')
                    ->badge()
                    ->color('primary')
                    ->size('lg')
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('mesa.numero')
                    ->label('Mesa')
                    ->badge()
                    ->color('info')
                    ->size('lg'),
                    
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('detalles')
                    ->label('Productos')
                    ->formatStateUsing(function ($record) {
                        return $record->detalles->map(function ($detalle) {
                            return $detalle->cantidad . 'x ' . $detalle->producto->nombre;
                        })->join(', ');
                    })
                    ->wrap()
                    ->limit(100),
                    
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_preparacion',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => '⏳ Pendiente',
                        'en_preparacion' => '👨‍🍳 En Preparación',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Hora')
                    ->dateTime('H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('preparar')
                    ->label('Preparar')
                    ->icon('heroicon-o-fire')
                    ->color('warning')
                    ->action(function ($record) {
                        $record->update(['estado' => 'en_preparacion']);
                    })
                    ->visible(fn ($record) => $record->estado === 'pendiente'),
                    
                Tables\Actions\Action::make('listo')
                    ->label('Listo')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['estado' => 'listo']);
                    })
                    ->visible(fn ($record) => $record->estado === 'en_preparacion'),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s'); // Auto-refresh cada 10 segundos
    }
}