<?php

namespace App\Filament\Resources\UserAssessmentResource\Pages;

use App\Filament\Resources\UserAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserAssessment extends EditRecord
{
    protected static string $resource = UserAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
