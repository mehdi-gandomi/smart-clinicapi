<?php

namespace App\Filament\Resources\OnlineVisitResource\Pages;

use App\Filament\Resources\OnlineVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOnlineVisits extends ListRecords
{
    protected static string $resource = OnlineVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
