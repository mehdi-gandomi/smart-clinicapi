<?php

namespace App\Filament\Resources\UserMedicalDocResource\Pages;

use App\Filament\Resources\UserMedicalDocResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserMedicalDoc extends EditRecord
{
    protected static string $resource = UserMedicalDocResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
