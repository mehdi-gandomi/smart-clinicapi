<?php

namespace App\Filament\Resources\UserBloodPressureResource\Pages;

use App\Filament\Resources\UserBloodPressureResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use App\Filament\Resources\UserResource\Widgets\BloodPressureChart;
class ViewUserBloodPressure extends ViewRecord
{
    protected static string $resource = UserBloodPressureResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            BloodPressureChart::class,
        ];
}
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Personal Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('first_name')
                                    ->label('First Name')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('last_name')
                                    ->label('Last Name')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('mobile')
                                    ->label('Mobile')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('gender')
                                    ->label('Gender')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'male' => 'success',
                                        'female' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        '1' => 'Male',
                                        '2' => 'Female',
                                        default => $state,
                                    }),
                                TextEntry::make('birth_date')
                                    ->label('Birth Date')
                                    ->date('Y/m/d'),
                                TextEntry::make('created_at')
                                    ->label('Registration Date')
                                    ->dateTime('Y/m/d H:i:s'),
                            ]),
                    ]),

                // Section::make('Blood Pressure Information')
                //     ->schema([
                //         Grid::make(2)
                //             ->schema([
                //                 TextEntry::make('blood_pressures_count')
                //                     ->label('Total Blood Pressure Records')
                //                     ->size(TextEntry\TextEntrySize::Large),
                //                 TextEntry::make('blood_pressures_max_date')
                //                     ->label('Last Blood Pressure Record')
                //                     ->dateTime('Y/m/d H:i:s'),
                //             ]),
                //     ]),

                Section::make('Medical Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('height')
                                    ->label('Height')
                                    ->suffix(' cm'),
                                TextEntry::make('weight')
                                    ->label('Weight')
                                    ->suffix(' kg'),
                                TextEntry::make('bmi')
                                    ->label('Body Mass Index (BMI)')
                                    ->state(function ($record) {
                                        if ($record->height && $record->weight) {
                                            $heightInMeters = $record->height / 100;
                                            $bmi = $record->weight / ($heightInMeters * $heightInMeters);
                                            return number_format($bmi, 1);
                                        }
                                        return null;
                                    }),
                                TextEntry::make('blood_type')
                                    ->label('Blood Type')
                                    ->badge(),
                            ]),
                    ]),

                Section::make('Contact Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('address')
                                    ->label('Address')
                                    ->columnSpanFull(),
                                TextEntry::make('emergency_contact')
                                    ->label('Emergency Contact Number'),
                                TextEntry::make('emergency_contact_name')
                                    ->label('Emergency Contact Name'),
                            ]),
                    ]),
            ]);
    }
} 