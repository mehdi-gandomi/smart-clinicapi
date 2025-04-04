<?php

namespace App\Filament\Resources\UserAssessmentResource\Pages;

use App\Filament\Resources\UserAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserAssessments extends ListRecords
{
    protected static string $resource = UserAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
