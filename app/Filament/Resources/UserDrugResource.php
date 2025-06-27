<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserDrugResource\Pages;
use App\Filament\Resources\UserDrugResource\RelationManagers;
use App\Models\UserDrug;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;

class UserDrugResource extends Resource
{
    protected static ?string $model = UserDrug::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Docs management';
    protected static ?string $label = 'Drug';
    protected static ?string $pluralLabel = 'Drugs';
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
    public static function canViewAny(): bool
    {
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->label(__('User')),
                        Components\Select::make('drug_type')
                            ->options([
                                'prescription' => __('Prescription'),
                                'other' => __('Other'),
                            ])
                            ->required()
                            ->label(__('Drug Type')),
                        Components\FileUpload::make('files')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->maxSize(10240)
                            ->directory('drug-docs')
                            ->label(__('Files'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('User')),
                // Tables\Columns\TextColumn::make('drug_type')
                //     ->badge()
                //     ->color(fn (string $state): string => match ($state) {
                //         'prescription' => 'success',
                //         'other' => 'warning',
                //         default => 'gray',
                //     })
                //     ->formatStateUsing(fn (string $state): string => match ($state) {
                //         'prescription' => __('Prescription'),
                //         'other' => __('Other'),
                //         default => $state,
                //     })
                //     ->sortable()
                //     ->label(__('Drug Type')),
                Tables\Columns\TextColumn::make('files_count')
                    ->state(function ($record): int {
                        return count($record->files ?? []);
                    })
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->label(__('Files Count')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label(__('Upload Date')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('drug_type')
                    ->options([
                        'prescription' => __('Prescription'),
                        'other' => __('Other'),
                    ])
                    ->label(__('Drug Type')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (UserDrug $record) {
                            if (!empty($record->files)) {
                                foreach ($record->files as $file) {
                                    Storage::disk('public')->delete($file['path']);
                                }
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                if (!empty($record->files)) {
                                    foreach ($record->files as $file) {
                                        Storage::disk('public')->delete($file['path']);
                                    }
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('Drug Information'))
                    ->schema([
                        TextEntry::make('user.name')
                            ->label(__('User')),
                        TextEntry::make('drug_type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'prescription' => 'success',
                                'other' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'prescription' => __('Prescription'),
                                'other' => __('Other'),
                                default => $state,
                            })
                            ->label(__('Drug Type')),
                        TextEntry::make('created_at')
                            ->dateTime('Y-m-d H:i:s')
                            ->label(__('Upload Date')),
                    ])
                    ->columns(3),

                Section::make(__('Files'))
                    ->schema([
                        ViewEntry::make('files')
                            ->view('filament.resources.user-drug.drug-docs-view')
                            ->columnSpanFull(),
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
            'index' => Pages\ListUserDrugs::route('/'),
            'create' => Pages\CreateUserDrug::route('/create'),
            'view' => Pages\ViewUserDrug::route('/{record}'),
            'edit' => Pages\EditUserDrug::route('/{record}/edit'),
        ];
    }
}
