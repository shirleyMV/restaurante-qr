<?php

namespace App\Filament\Resources\MesaResource\Pages;

use App\Filament\Resources\MesaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMesa extends ViewRecord
{
    protected static string $resource = MesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
