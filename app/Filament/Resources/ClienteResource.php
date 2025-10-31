<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClienteResource extends Resource
{
    /**
     * Administradores y cajeras pueden ver clientes
     */
    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    /**
     * Cajera y admin pueden crear clientes
     */
    public static function canCreate(): bool
    {
        return auth()->check();
    }

    /**
     * Cajera y admin pueden editar clientes
     */
    public static function canEdit($record): bool
    {
        return auth()->check();
    }

    /**
     * Solo administradores pueden eliminar
     */
    public static function canDelete($record): bool
    {
        return auth()->user()?->esAdministrador() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->esAdministrador() ?? false;
    }

    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Clientes';
    
    protected static ?string $navigationGroup = 'Ventas';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('telefono')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(20),
                    
                Forms\Components\TextInput::make('correo')
                    ->label('Correo Electrónico')
                    ->email()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->icon('heroicon-o-phone'),
                    
                Tables\Columns\TextColumn::make('correo')
                    ->label('Correo')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),
                    
                // Tables\Columns\TextColumn::make('pedidos_count')
                //     ->label('Pedidos')
                //     ->counts('pedidos')
                //     ->badge()
                //     ->color('success'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'view' => Pages\ViewCliente::route('/{record}'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}