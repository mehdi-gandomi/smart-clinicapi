<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OnlineVisitResource\Pages;
use App\Filament\Resources\OnlineVisitResource\RelationManagers;
use App\Models\OnlineVisit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class OnlineVisitResource extends Resource
{
    protected static ?string $model = OnlineVisit::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationGroup = 'مدیریت بیماران';

    protected static ?string $navigationLabel = 'ویزیت های آنلاین';

    protected static ?string $modelLabel = 'ویزیت آنلاین';

    protected static ?string $pluralModelLabel = 'ویزیت های آنلاین';

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
                Section::make('اطلاعات ویزیت')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->label('نام بیمار'),
                        Forms\Components\Select::make('visit_type')
                            ->options([
                                'medical_questions' => 'سوالات پزشکی',
                                'document_review' => 'بررسی مدارک',
                                'prescription_renewal' => 'تمدید نسخه',
                            ])
                            ->required()
                            ->label('نوع ویزیت'),
                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'در انتظار بررسی',
                                'in_progress' => 'در حال بررسی',
                                'answered' => 'پاسخ داده شده',
                                'cancelled' => 'لغو شده',
                            ])
                            ->required()
                            ->label('وضعیت'),
                    ])
                    ->columns(2),

                Section::make('مدارک پزشکی')
                    ->schema([
                        ViewField::make('medical_documents')
                            ->view('filament.resources.online-visit.medical-docs')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('نام بیمار')
                    ->searchable(),
                Tables\Columns\TextColumn::make('visit_type')
                    ->label('نوع ویزیت')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'medical_questions' => 'سوالات پزشکی',
                        'document_review' => 'بررسی مدارک',
                        'prescription_renewal' => 'تمدید نسخه',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'answered' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'در انتظار بررسی',
                        'in_progress' => 'در حال بررسی',
                        'answered' => 'پاسخ داده شده',
                        'cancelled' => 'لغو شده',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ درخواست')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار بررسی',
                        'in_progress' => 'در حال بررسی',
                        'answered' => 'پاسخ داده شده',
                        'cancelled' => 'لغو شده',
                    ]),
                Tables\Filters\SelectFilter::make('visit_type')
                    ->label('نوع ویزیت')
                    ->options([
                        'medical_questions' => 'سوالات پزشکی',
                        'document_review' => 'بررسی مدارک',
                        'prescription_renewal' => 'تمدید نسخه',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListOnlineVisits::route('/'),
            'create' => Pages\CreateOnlineVisit::route('/create'),
            'view' => Pages\ViewOnlineVisit::route('/{record}'),
            'edit' => Pages\EditOnlineVisit::route('/{record}/edit'),
        ];
    }
}
