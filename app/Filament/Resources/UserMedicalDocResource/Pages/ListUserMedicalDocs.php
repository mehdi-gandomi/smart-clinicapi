<?php

namespace App\Filament\Resources\UserMedicalDocResource\Pages;

use App\Filament\Resources\UserMedicalDocResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserMedicalDocs extends ListRecords
{
    protected static string $resource = UserMedicalDocResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
