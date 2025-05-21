<?php

namespace App\Filament\Pages;

use App\Settings\FinancialSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageFinancialSetting extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'تنظیمات مالی';
    protected static ?string $title = 'تنظیمات مالی';
    protected static ?int $navigationSort = 3;

    protected static string $settings = FinancialSetting::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('قیمت‌ها')
                    ->schema([
                        Forms\Components\TextInput::make('blood_pressure_price')
                            ->label('قیمت ثبت فشار خون')
                            ->numeric()
                            ->prefix('تومان')
                            ->required()
                            ->minValue(0)
                            ->helperText('قیمت هر بار ثبت فشار خون'),

                        Forms\Components\TextInput::make('online_visit_price')
                            ->label('قیمت ویزیت آنلاین')
                            ->numeric()
                            ->prefix('تومان')
                            ->required()
                            ->minValue(0)
                            ->helperText('قیمت هر ویزیت آنلاین'),
                    ])
                    ->columns(2),
            ]);
    }
}
