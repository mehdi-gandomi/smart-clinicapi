<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\UserResource\RelationManagers\UserAssessmentsRelationManager;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    public static function canViewAny(): bool
    {
        return auth()->user()->user_type === 'admin';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->user_type === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Login Information'))
                    ->description(__('These credentials will be used for login'))
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->label(__('First Name')),
                        TextInput::make('last_name')
                            ->required()
                            ->label(__('Last Name')),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label(__('Email')),
                        Select::make('user_type')
                            ->options([
                                'admin' => __('Admin'),
                                'user' => __('User'),
                                'doctor' => __('Doctor'),
                            ])
                            ->required()
                            ->default('user')
                            ->label(__('User Type')),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->label(__('Password'))
                            ->confirmed(),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->label(__('Confirm Password'))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('Contact Information'))
                    ->schema([
                        TextInput::make('mobile')
                            ->tel()

                            ->label(__('Mobile')),
                        TextInput::make('address')
                            ->label(__('Address'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('Personal Details'))
                    ->schema([
                        TextInput::make('national_id')
                            ->label(__('National ID')),
                        Select::make('gender')
                            ->options([
                                '1' => __('Male'),
                                '2' => __('Female'),
                            ])
                            ->default(1)
                            ->label(__('Gender')),
                        TextInput::make('age')
                            ->numeric()
                            ->label(__('Age')),
                        TextInput::make('weight')
                            ->numeric()
                            ->label(__('Weight (kg)')),
                        TextInput::make('height')
                            ->numeric()
                            ->label(__('Height (cm)')),
                        TextInput::make('occupation')
                            ->label(__('Occupation')),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('Insurance Information'))
                    ->schema([
                        TextInput::make('primary_insurance')
                            ->label(__('Primary Insurance')),
                        TextInput::make('supplementary_insurance')
                            ->label(__('Supplementary Insurance')),
                    ])
                    ->columns(2)
                    ->collapsible(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable()
                    ->label(__('First Name')),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable()
                    ->label(__('Last Name')),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label(__('Email')),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable()
                    ->sortable()
                    ->label(__('Mobile')),
                Tables\Columns\TextColumn::make('gender')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'male' => __('Male'),
                        'female' => __('Female'),
                        default => '---',
                    })
                    ->sortable()
                    ->label(__('Gender')),
                Tables\Columns\IconColumn::make('done_assessment')
                    ->boolean()
                    ->label(__('Assessment Completed')),
                Tables\Columns\TextColumn::make('done_assessment_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label(__('Assessment Completion Date')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label(__('Registration Date')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => __('Male'),
                        'female' => __('Female'),
                    ])
                    ->label(__('Gender')),
                Tables\Filters\TernaryFilter::make('done_assessment')
                    ->label(__('Assessment Completed')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Impersonate::make()->redirectTo(route('filament.admin.pages.dashboard')), // <---
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UserAssessmentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make(__('Personal Information'))
                    ->schema([
                        TextEntry::make('first_name')
                            ->label(__('First Name')),
                        TextEntry::make('last_name')
                            ->label(__('Last Name')),
                        TextEntry::make('email')
                            ->label(__('Email')),
                        TextEntry::make('mobile')
                            ->label(__('Mobile')),
                        TextEntry::make('national_id')
                            ->label(__('National ID')),
                        TextEntry::make('gender')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'male' => __('Male'),
                                'female' => __('Female'),
                                default => '---',
                            })
                            ->label(__('Gender')),
                        TextEntry::make('age')
                            ->label(__('Age')),
                        TextEntry::make('weight')
                            ->label(__('Weight (kg)')),
                        TextEntry::make('height')
                            ->label(__('Height (cm)')),
                    ])
                    ->columns(3),

                InfolistSection::make(__('Insurance Information'))
                    ->schema([
                        TextEntry::make('primary_insurance')
                            ->label(__('Primary Insurance')),
                        TextEntry::make('supplementary_insurance')
                            ->label(__('Supplementary Insurance')),
                    ])
                    ->columns(2),

                InfolistSection::make(__('Additional Information'))
                    ->schema([
                        TextEntry::make('occupation')
                            ->label(__('Occupation')),
                        TextEntry::make('address')
                            ->label(__('Address'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
