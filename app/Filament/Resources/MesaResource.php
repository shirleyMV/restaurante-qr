<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MesaResource\Pages;
use App\Filament\Resources\MesaResource\RelationManagers;
use App\Models\Mesa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MesaResource extends Resource
{
    protected static ?string $model = Mesa::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    
    protected static ?string $navigationLabel = 'Mesas';
    
    protected static ?string $navigationGroup = 'Configuración';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('numero')
                    ->label('Número de Mesa')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->minValue(1)
                    ->maxValue(999),
                    
                Forms\Components\TextInput::make('capacidad')
                    ->label('Capacidad (personas)')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(20)
                    ->default(4),
                    
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'disponible' => 'Disponible',
                        'ocupada' => 'Ocupada',
                        'reservada' => 'Reservada',
                    ])
                    ->required()
                    ->default('disponible'),
                    
                Forms\Components\TextInput::make('codigo_qr')
                    ->label('Código QR')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Se generará automáticamente al crear la mesa'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->label('Mesa N°')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->size('lg'),
                    
                Tables\Columns\TextColumn::make('capacidad')
                    ->label('Capacidad')
                    ->suffix(' personas')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'disponible',
                        'danger' => 'ocupada',
                        'warning' => 'reservada',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'disponible' => 'Disponible',
                        'ocupada' => 'Ocupada',
                        'reservada' => 'Reservada',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('codigo_qr')
                    ->label('Código QR')
                    ->limit(20)
                    ->copyable()
                    ->copyMessage('Código copiado')
                    ->tooltip('Click para copiar'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'disponible' => 'Disponible',
                        'ocupada' => 'Ocupada',
                        'reservada' => 'Reservada',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('ver_qr')
                    ->label('Ver QR')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading(fn ($record) => 'QR Mesa ' . $record->numero)
                    ->modalContent(fn ($record) => view('filament.modals.mesa-qr', ['mesa' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->color('primary'),
                    
                Tables\Actions\Action::make('cambiar_estado')
                    ->label('Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('estado')
                            ->label('Nuevo Estado')
                            ->options([
                                'disponible' => 'Disponible',
                                'ocupada' => 'Ocupada',
                                'reservada' => 'Reservada',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['estado' => $data['estado']]);
                    })
                    ->color('warning'),
                    
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('numero', 'asc');
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
            'index' => Pages\ListMesas::route('/'),
            'create' => Pages\CreateMesa::route('/create'),
            'view' => Pages\ViewMesa::route('/{record}'),
            'edit' => Pages\EditMesa::route('/{record}/edit'),
        ];
    }    
}
