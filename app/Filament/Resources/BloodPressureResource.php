<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BloodPressureResource\Pages;
use App\Filament\Resources\BloodPressureResource\RelationManagers;
use App\Models\BloodPressure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BloodPressureResource extends Resource
{
    protected static ?string $model = BloodPressure::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBloodPressures::route('/'),
            'create' => Pages\CreateBloodPressure::route('/create'),
            'edit' => Pages\EditBloodPressure::route('/{record}/edit'),
        ];
    }
}
