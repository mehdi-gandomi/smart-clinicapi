<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserBloodPressureResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class UserBloodPressureResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Blood Pressures';

    protected static ?string $modelLabel = 'User Blood Pressure';

    protected static ?string $pluralModelLabel = 'User Blood Pressures';

    public static function canViewAny(): bool
    {
        return auth()->user()->user_type === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form schema is not needed as we're only viewing
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereHas('bloodPressures')
                    ->withCount('bloodPressures')
                    ->withMax('bloodPressures', 'date')
            )
            ->columns([
                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable()
                    ->label(__('First Name')),
                TextColumn::make('last_name')
                    ->searchable()
                    ->sortable()
                    ->label(__('Last Name')),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label(__('Email')),
                TextColumn::make('mobile')
                    ->searchable()
                    ->sortable()
                    ->label(__('Mobile')),
                TextColumn::make('blood_pressures_count')
                    ->counts('bloodPressures')
                    ->sortable()
                    ->label(__('Total Readings')),
                TextColumn::make('blood_pressures_max_date')
                    ->max('bloodPressures', 'date')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label(__('Last Reading')),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->options([
                        'male' => __('Male'),
                        'female' => __('Female'),
                    ])
                    ->label(__('Gender')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions needed
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserBloodPressures::route('/'),
            'view' => Pages\ViewUserBloodPressure::route('/{record}'),
        ];
    }
    public static function getNavigationBadge(): ?string { 
        return (string) User::count();
    }
    public static function getNavigationSort(): ?int { 
        return 0;
     }
} 