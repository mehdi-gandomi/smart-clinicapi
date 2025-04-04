<?php

namespace App\Filament\Resources\UserDrugResource\Pages;

use App\Filament\Resources\UserDrugResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserDrugs extends ListRecords
{
    protected static string $resource = UserDrugResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
