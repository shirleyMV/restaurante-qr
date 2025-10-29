<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ViewColumn;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationLabel = 'Productos';
    
    protected static ?string $navigationGroup = 'Menú';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('categoria_id')
                ->label('Categoría')
                ->relationship('categoria', 'nombre')
                ->required()
                ->preload()
                ->searchable(),
                
            Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
                
            Forms\Components\Textarea::make('descripcion')
                ->maxLength(65535)
                ->columnSpanFull(),
                
            Forms\Components\TextInput::make('precio')
                ->required()
                ->numeric()
                ->prefix('Bs')
                ->minValue(0)
                ->step(0.01),
                
            Forms\Components\FileUpload::make('imagen')
                ->label('Imagen del Producto')
                ->image()
                ->directory('productos')
                ->disk('public_uploads')
                ->visibility('public')
                ->imageEditor()
                ->maxSize(2048)
                ->columnSpanFull(),
                
            Forms\Components\Toggle::make('disponible')
                ->label('¿Disponible?')
                ->required()
                ->default(true)
                ->inline(false),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('ID')
                ->sortable(),
                
            // CAMBIO AQUÍ: Mostrar imagen desde public
            Tables\Columns\ImageColumn::make('imagen')
                ->label('Foto')
                ->disk('public_uploads')
                ->width(60)
                ->height(60)
                ->circular(),
                
            Tables\Columns\TextColumn::make('nombre')
                ->searchable()
                ->sortable()
                ->weight('bold'),
                
            Tables\Columns\TextColumn::make('categoria.nombre')
                ->label('Categoría')
                ->badge()
                ->color('info')
                ->sortable(),
                
            Tables\Columns\TextColumn::make('precio')
                ->money('BOB')
                ->sortable()
                ->weight('bold')
                ->color('success'),
                
            Tables\Columns\IconColumn::make('disponible')
                ->label('Disponible')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('categoria')
                ->relationship('categoria', 'nombre')
                ->label('Categoría')
                ->preload(),
                
            Tables\Filters\TernaryFilter::make('disponible')
                ->label('Disponibilidad')
                ->placeholder('Todos')
                ->trueLabel('Solo disponibles')
                ->falseLabel('Solo agotados'),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ])
        ->defaultSort('nombre', 'asc');
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'view' => Pages\ViewProducto::route('/{record}'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
