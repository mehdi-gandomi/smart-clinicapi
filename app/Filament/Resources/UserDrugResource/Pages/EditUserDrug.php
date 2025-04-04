<?php

namespace App\Filament\Resources\UserDrugResource\Pages;

use App\Filament\Resources\UserDrugResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserDrug extends EditRecord
{
    protected static string $resource = UserDrugResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
