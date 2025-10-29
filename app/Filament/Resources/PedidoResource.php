<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Filament\Resources\PedidoResource\RelationManagers;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'Pedidos';
    
    protected static ?string $navigationGroup = 'Ventas';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Pedido')
                    ->schema([
                        Forms\Components\Select::make('mesa_id')
                            ->label('Mesa')
                            ->relationship('mesa', 'numero')
                            ->required()
                            ->searchable()
                            ->columnSpan(1),
                            
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre')
                                    ->required(),
                                Forms\Components\TextInput::make('telefono')
                                    ->label('Teléfono'),
                                Forms\Components\TextInput::make('correo')
                                    ->label('Correo')
                                    ->email(),
                            ])
                            ->columnSpan(1),
                            
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'en_preparacion' => 'En Preparación',
                                'listo' => 'Listo',
                                'entregado' => 'Entregado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required()
                            ->default('pendiente')
                            ->columnSpan(1),
                            
                        Forms\Components\Select::make('metodo_pago')
                            ->label('Método de Pago')
                            ->options([
                                'efectivo' => 'Efectivo',
                                'qr' => 'QR',
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(4),
                    
                Forms\Components\Section::make('Productos del Pedido')
                    ->schema([
                        Forms\Components\Repeater::make('detalles')
                            ->relationship('detalles')
                            ->schema([
                                Forms\Components\Select::make('producto_id')
                                    ->label('Producto')
                                    ->relationship('producto', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $producto = \App\Models\Producto::find($state);
                                            if ($producto) {
                                                $set('precio_unitario', $producto->precio);
                                            }
                                        }
                                    })
                                    ->columnSpan(2),
                                    
                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->columnSpan(1),
                                    
                                Forms\Components\TextInput::make('precio_unitario')
                                    ->label('Precio Unit.')
                                    ->numeric()
                                    ->required()
                                    ->prefix('Bs')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar Producto')
                            ->collapsible()
                            ->cloneable(),
                    ]),
                    
                Forms\Components\Section::make('Total')
                    ->schema([
                        Forms\Components\Placeholder::make('total_info')
                            ->label('')
                            ->content('El total se calculará automáticamente al guardar el pedido'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('mesa.numero')
                    ->label('Mesa')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable()
                    ->description(fn ($record) => $record->cliente?->telefono)
                    ->icon('heroicon-o-user'),
                    
                Tables\Columns\TextColumn::make('detalles')
                    ->label('Productos')
                    ->formatStateUsing(function ($record) {
                        return $record->detalles->map(function ($detalle) {
                            return $detalle->cantidad . 'x ' . $detalle->producto->nombre;
                        })->join(', ');
                    })
                    ->wrap()
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->detalles->map(function ($detalle) {
                            return $detalle->cantidad . 'x ' . $detalle->producto->nombre . ' (Bs ' . number_format($detalle->precio, 2) . ')';
                        })->join('\n');
                    }),
                    
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('BOB')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                    
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Método de Pago')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'efectivo' => 'Efectivo',
                        'qr' => 'QR',
                        default => '-',
                    })
                    ->badge()
                    ->icon(fn (?string $state): string => match ($state) {
                        'efectivo' => 'heroicon-o-banknotes',
                        'qr' => 'heroicon-o-qr-code',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'efectivo' => 'success',
                        'qr' => 'info',
                        default => 'gray',
                    }),
                    
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_preparacion',
                        'info' => 'listo',
                        'success' => 'entregado',
                        'danger' => 'cancelado',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'en_preparacion' => 'En Preparación',
                        'listo' => 'Listo',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_preparacion' => 'En Preparación',
                        'listo' => 'Listo',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                    ]),
                    
                Tables\Filters\SelectFilter::make('mesa')
                    ->relationship('mesa', 'numero')
                    ->label('Mesa'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cambiar_estado')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('estado')
                            ->label('Nuevo Estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'en_preparacion' => 'En Preparación',
                                'listo' => 'Listo',
                                'entregado' => 'Entregado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['estado' => $data['estado']]);
                    })
                    ->requiresConfirmation()
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'view' => Pages\ViewPedido::route('/{record}'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }    
}
