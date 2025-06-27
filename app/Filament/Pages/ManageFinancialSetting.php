<?php

namespace App\Filament\Pages;

use App\Settings\FinancialSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageFinancialSetting extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Financial Settings';
    protected static ?string $title = 'Financial Settings';
    protected static ?int $navigationSort = 3;

    protected static string $settings = FinancialSetting::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Prices')
                    ->schema([
                        Forms\Components\TextInput::make('blood_pressure_price')
                            ->label('Blood Pressure Recording Price')
                            ->numeric()
                            ->prefix('Toman')
                            ->required()
                            ->minValue(0)
                            ->helperText('Price for each blood pressure recording'),

                        Forms\Components\TextInput::make('online_visit_price')
                            ->label('Online Visit Price')
                            ->numeric()
                            ->prefix('Toman')
                            ->required()
                            ->minValue(0)
                            ->helperText('Price for each online visit'),
                    ])
                    ->columns(2),
            ]);
    }
    public static function canAccess(): bool
{
    return auth()->user()->user_type == 'admin';
}
}
