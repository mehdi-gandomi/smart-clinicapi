<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserMedicalDocResource\Pages;
use App\Models\UserMedicalDoc;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables;
use Filament\Forms\Components;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;

class UserMedicalDocResource extends Resource
{
    protected static ?string $model = UserMedicalDoc::class;
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Docs management';
    protected static ?string $label = 'Doc';
    protected static ?string $pluralLabel = 'Medical docs';
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
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
                            ->label('کاربر'),
                        Components\Select::make('doc_type')
                            ->options([
                                'blood_test' => 'آزمایش خون',
                                'other' => 'سایر مدارک',
                            ])
                            ->required()
                            ->label('نوع مدرک'),
                        // Components\Textarea::make('description')
                        //     ->required()
                        //     ->label('توضیحات'),
                        Components\FileUpload::make('files')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->maxSize(10240)
                            ->directory('medical-docs')
                            ->label('فایل‌ها')
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
                Tables\Columns\TextColumn::make('doc_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'blood_test' => 'success',
                        'other' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'blood_test' => __('Blood Test'),
                        'other' => __('Other Documents'),
                        default => $state,
                    })
                    ->sortable()
                    ->label(__('Document Type')),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->wrap()
                    ->label(__('Description')),
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
                Tables\Filters\SelectFilter::make('doc_type')
                    ->options([
                        'blood_test' => 'آزمایش خون',
                        'other' => 'سایر مدارک',
                    ])
                    ->label('نوع مدرک'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (UserMedicalDoc $record) {
                            // Delete files from storage when deleting record
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
                            // Delete files from storage when bulk deleting records
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserMedicalDocs::route('/'),
            'create' => Pages\CreateUserMedicalDoc::route('/create'),
            'view' => Pages\ViewUserMedicalDoc::route('/{record}'),
            'edit' => Pages\EditUserMedicalDoc::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('Document Information'))
                    ->schema([
                        TextEntry::make('user.name')
                            ->label(__('User')),
                        TextEntry::make('doc_type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'blood_test' => 'success',
                                'other' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'blood_test' => __('Blood Test'),
                                'other' => __('Other Documents'),
                                default => $state,
                            })
                            ->label(__('Document Type')),
                        TextEntry::make('created_at')
                            ->dateTime('Y-m-d H:i:s')
                            ->label(__('Upload Date')),
                    ])
                    ->columns(3),

                Section::make(__('Files'))
                    ->schema([
                        ViewEntry::make('files')
                            ->view('filament.resources.user-medical-doc.medical-docs-view')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
