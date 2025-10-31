<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoResource\Pages;
use App\Models\Pago;
use App\Events\PagoConfirmado;
use App\Events\PedidoConfirmado;
use App\Events\PedidoCancelado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationLabel = 'Pagos';
    
    protected static ?string $navigationGroup = 'Ventas';
    
    protected static ?int $navigationSort = 4;

    /**
     * Solo administradores y cajeras pueden ver pagos
     */
    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    /**
     * Cajera y admin pueden crear pagos manualmente
     */
    public static function canCreate(): bool
    {
        return auth()->check();
    }

    /**
     * Cajera y admin pueden editar pagos
     */
    public static function canEdit(Model $record): bool
    {
        return auth()->check();
    }

    /**
     * Solo administradores pueden eliminar
     */
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->esAdministrador();
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->esAdministrador();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Pago')
                    ->schema([
                        Forms\Components\Select::make('pedido_id')
                            ->label('Pedido')
                            ->relationship('pedido', 'id')
                            ->required()
                            ->disabled(),
                            
                        Forms\Components\Select::make('metodo_pago_id')
                            ->label('Método de Pago')
                            ->relationship('metodoPago', 'nombre')
                            ->required()
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('monto')
                            ->label('Monto')
                            ->numeric()
                            ->prefix('Bs')
                            ->required()
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('codigo_transaccion')
                            ->label('Código de Transacción')
                            ->maxLength(255)
                            ->placeholder('Opcional'),
                            
                        Forms\Components\FileUpload::make('comprobante')
                            ->label('Comprobante de Pago')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('comprobantes')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull()
                            ->helperText('Sube la imagen del comprobante de Yape/QR'),
                            
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'completado' => 'Completado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required()
                            ->default('pendiente'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Confirmación')
                    ->schema([
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas de Confirmación')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record && !$record->estaConfirmado()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('pedido.id')
                    ->label('Pedido')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('pedido.cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('metodoPago.nombre')
                    ->label('Método')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Efectivo' => 'success',
                        'QR' => 'info',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->money('BOB')
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\ImageColumn::make('comprobante')
                    ->label('Comprobante')
                    ->disk('public')
                    ->square()
                    ->size(60)
                    ->defaultImageUrl(url('/images/no-image.png')),
                    
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'completado',
                        'danger' => 'cancelado',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'completado' => 'Completado',
                        'cancelado' => 'Cancelado',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('confirmadoPor.name')
                    ->label('Confirmado por')
                    ->default('Sin confirmar')
                    ->icon(fn ($record) => $record->confirmado_por ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                    ->color(fn ($record) => $record->confirmado_por ? 'success' : 'warning'),
                    
                Tables\Columns\TextColumn::make('fecha_confirmacion')
                    ->label('Fecha Confirmación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('fecha_pago')
                    ->label('Fecha Pago')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'completado' => 'Completado',
                        'fallido' => 'Fallido',
                    ]),
                    
                Tables\Filters\SelectFilter::make('metodo_pago_id')
                    ->label('Método de Pago')
                    ->relationship('metodoPago', 'nombre'),
                    
                Tables\Filters\Filter::make('sin_confirmar')
                    ->label('Sin Confirmar')
                    ->query(fn (Builder $query): Builder => $query->whereNull('confirmado_por')),
                    
                Tables\Filters\Filter::make('confirmados')
                    ->label('Confirmados')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('confirmado_por')),
            ])
            ->actions([
                Tables\Actions\Action::make('confirmar')
                    ->label('Confirmar Pago')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Pago $record): bool => !$record->estaConfirmado() && $record->estado !== 'cancelado')
                    ->form([
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas (opcional)')
                            ->placeholder('Ej: Efectivo recibido, QR confirmado, etc.')
                            ->maxLength(500),
                    ])
                    ->action(function (Pago $record, array $data): void {
                        $record->confirmar(auth()->id(), $data['notas'] ?? null);
                        
                        Notification::make()
                            ->title('Pago confirmado')
                            ->success()
                            ->body('El pago ha sido confirmado exitosamente.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Pago')
                    ->modalDescription('¿Estás seguro de que deseas confirmar este pago?')
                    ->modalSubmitActionLabel('Sí, confirmar'),
                    
                Tables\Actions\Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Pago $record): bool => $record->estado !== 'cancelado')
                    ->form([
                        Forms\Components\Select::make('motivo')
                            ->label('Motivo de cancelación')
                            ->options([
                                'Cliente solicitó cancelación' => 'Cliente solicitó cancelación',
                                'Error en el pedido' => 'Error en el pedido',
                                'Producto no disponible' => 'Producto no disponible',
                                'Pago duplicado' => 'Pago duplicado',
                                'Otro' => 'Otro',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Textarea::make('motivo_detalle')
                            ->label('Detalles adicionales (opcional)')
                            ->placeholder('Especifica más detalles si es necesario')
                            ->maxLength(500)
                            ->visible(fn (Forms\Get $get) => $get('motivo') === 'Otro'),
                    ])
                    ->action(function (Pago $record, array $data): void {
                        $motivo = $data['motivo'];
                        if ($data['motivo'] === 'Otro' && !empty($data['motivo_detalle'])) {
                            $motivo .= ': ' . $data['motivo_detalle'];
                        }
                        
                        // Cancelar pago
                        $record->estado = 'cancelado';
                        $record->motivo_cancelacion = $motivo;
                        $record->cancelado_por = auth()->id();
                        $record->fecha_cancelacion = now();
                        $record->save();
                        
                        // Cancelar pedido asociado
                        $pedido = $record->pedido;
                        $pedido->estado = 'cancelado';
                        $pedido->motivo_cancelacion = $motivo;
                        $pedido->cancelado_por = auth()->id();
                        $pedido->fecha_cancelacion = now();
                        $pedido->save();
                        
                        // Devolver stock
                        foreach ($pedido->detalles as $detalle) {
                            $producto = $detalle->producto;
                            $producto->stock += $detalle->cantidad;
                            $producto->save();
                        }
                        
                        // Disparar evento
                        event(new PedidoCancelado($pedido));
                        
                        Notification::make()
                            ->title('Pago cancelado')
                            ->success()
                            ->body('El pago y pedido han sido cancelados. Stock devuelto.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Pago')
                    ->modalDescription('¿Estás seguro de que deseas cancelar este pago? Esta acción devolverá el stock.')
                    ->modalSubmitActionLabel('Sí, cancelar'),
                    
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No permitir acciones masivas para mayor seguridad
            ])
            ->defaultSort('created_at', 'desc');
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPagos::route('/'),
            'view' => Pages\ViewPago::route('/{record}'),
        ];
    }    
}
