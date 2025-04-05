<?php

namespace App\Filament\Pages;

use App\Settings\PromptSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;

class ManagePrompts extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = PromptSettings::class;

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Textarea::make('documents_prompt')
                ->label('Documents Analysis Prompt')
                ->required()
                ->rows(5)
                ->helperText('Template prompt used for analyzing documents'),

            Textarea::make('assessment_prompt')
                ->label('Assessment Analysis Prompt')
                ->required()
                ->rows(5)
                ->helperText('Template prompt used for analyzing assessments'),

            Textarea::make('full_prompt')
                ->label('Full Analysis Prompt')
                ->required()
                ->rows(5)
                ->helperText('Template prompt used for full analysis'),
        ]);
    }
}
