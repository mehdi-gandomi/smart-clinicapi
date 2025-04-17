<?php

namespace App\Filament\Resources\OnlineVisitResource\Pages;

use App\Filament\Resources\OnlineVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOnlineVisit extends EditRecord
{
    protected static string $resource = OnlineVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
