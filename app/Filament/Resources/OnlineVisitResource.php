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

    // protected static ?string $navigationGroup = 'Patient Management';

    protected static ?string $navigationLabel = 'Online Visits';

    protected static ?string $modelLabel = 'Online Visit';

    protected static ?string $pluralModelLabel = 'Online Visits';

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
                Section::make('Visit Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->label('Patient Name'),
                        Forms\Components\Select::make('visit_type')
                            ->options([
                                'medical_questions' => 'Medical Questions',
                                'document_review' => 'Document Review',
                                'prescription_renewal' => 'Prescription Renewal',
                            ])
                            ->required()
                            ->label('Visit Type'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Review',
                                'in_progress' => 'In Progress',
                                'answered' => 'Answered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->label('Status'),
                        Forms\Components\Textarea::make('answer')
                            ->label('Text Response')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('voice_answer')
                            ->label('Voice Response')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/mp3'])
                            ->maxSize(10240)
                            ->directory('voice-answers')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('voice_answer_duration')
                            ->label('Voice Response Duration')
                            ->placeholder('Example: 2:30')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Medical Documents')
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
                    ->label('Patient Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('visit_type')
                    ->label('Visit Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'medical_questions' => 'Medical Questions',
                        'document_review' => 'Document Review',
                        'prescription_renewal' => 'Prescription Renewal',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'medical_questions',
                        'success' => 'document_review',
                        'warning' => 'prescription_renewal',
                    ]),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending Review',
                        'in_progress' => 'In Progress',
                        'answered' => 'Answered',
                        'cancelled' => 'Cancelled',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'in_progress',
                        'success' => 'answered',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                Tables\Columns\IconColumn::make('answer')
                    ->label('Text Response')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\IconColumn::make('voice_answer')
                    ->label('Voice Response')
                    ->boolean()
                    ->trueIcon('heroicon-o-speaker-wave')
                    ->falseIcon('heroicon-o-speaker-x-mark'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending Review',
                        'in_progress' => 'In Progress',
                        'answered' => 'Answered',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('visit_type')
                    ->label('Visit Type')
                    ->options([
                        'medical_questions' => 'Medical Questions',
                        'document_review' => 'Document Review',
                        'prescription_renewal' => 'Prescription Renewal',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
    public static function getNavigationBadge(): ?string { 
        return (string) OnlineVisit::count();
    }
    public static function getNavigationSort(): ?int { 
        return 2;
     }
}
