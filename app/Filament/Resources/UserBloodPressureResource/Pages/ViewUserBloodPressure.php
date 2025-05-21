<?php

namespace App\Filament\Resources\UserBloodPressureResource\Pages;

use App\Filament\Resources\UserBloodPressureResource;
use App\Filament\Resources\UserResource\Widgets\BloodPressureChart;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserBloodPressure extends ViewRecord
{
    protected static string $resource = UserBloodPressureResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            BloodPressureChart::class,
        ];
    }
} 