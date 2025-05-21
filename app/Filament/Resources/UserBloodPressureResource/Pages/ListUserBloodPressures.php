<?php

namespace App\Filament\Resources\UserBloodPressureResource\Pages;

use App\Filament\Resources\UserBloodPressureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserBloodPressures extends ListRecords
{
    protected static string $resource = UserBloodPressureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action needed as we're only viewing
        ];
    }
} 