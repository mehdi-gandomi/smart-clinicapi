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
                Section::make('اطلاعات شخصی')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('first_name')
                                    ->label('نام')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('last_name')
                                    ->label('نام خانوادگی')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('email')
                                    ->label('ایمیل')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('mobile')
                                    ->label('موبایل')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('gender')
                                    ->label('جنسیت')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'male' => 'success',
                                        'female' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        '1' => 'مرد',
                                        '2' => 'زن',
                                        default => $state,
                                    }),
                                TextEntry::make('birth_date')
                                    ->label('تاریخ تولد')
                                    ->date('Y/m/d'),
                                TextEntry::make('created_at')
                                    ->label('تاریخ ثبت نام')
                                    ->dateTime('Y/m/d H:i:s'),
                            ]),
                    ]),

                // Section::make('اطلاعات فشار خون')
                //     ->schema([
                //         Grid::make(2)
                //             ->schema([
                //                 TextEntry::make('blood_pressures_count')
                //                     ->label('تعداد کل ثبت فشار خون')
                //                     ->size(TextEntry\TextEntrySize::Large),
                //                 TextEntry::make('blood_pressures_max_date')
                //                     ->label('آخرین ثبت فشار خون')
                //                     ->dateTime('Y/m/d H:i:s'),
                //             ]),
                //     ]),

                Section::make('اطلاعات پزشکی')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('height')
                                    ->label('قد (سانتی متر)')
                                    ->suffix(' cm'),
                                TextEntry::make('weight')
                                    ->label('وزن (کیلوگرم)')
                                    ->suffix(' kg'),
                                TextEntry::make('bmi')
                                    ->label('شاخص توده بدنی (BMI)')
                                    ->state(function ($record) {
                                        if ($record->height && $record->weight) {
                                            $heightInMeters = $record->height / 100;
                                            $bmi = $record->weight / ($heightInMeters * $heightInMeters);
                                            return number_format($bmi, 1);
                                        }
                                        return null;
                                    }),
                                TextEntry::make('blood_type')
                                    ->label('گروه خونی')
                                    ->badge(),
                            ]),
                    ]),

                Section::make('اطلاعات تماس')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('address')
                                    ->label('آدرس')
                                    ->columnSpanFull(),
                                TextEntry::make('emergency_contact')
                                    ->label('شماره تماس اضطراری'),
                                TextEntry::make('emergency_contact_name')
                                    ->label('نام تماس اضطراری'),
                            ]),
                    ]),
            ]);
    }
} 