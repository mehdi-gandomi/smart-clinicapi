<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAssessmentResource\Pages;
use App\Models\UserAssessment;
use App\Models\AssessmentSeries;
use App\Models\AssessmentQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Illuminate\Database\Eloquent\Builder;

class UserAssessmentResource extends Resource
{
    protected static ?string $model = UserAssessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Assessments';

    protected static ?string $modelLabel = 'Assessment';

    protected static ?string $pluralModelLabel = 'Assessments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Assessment Information'))
                    ->schema([
                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'first_name',
                                modifyQueryUsing: fn ($query) => $query->select(['id', 'first_name', 'last_name'])
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                            ->required()
                            ->searchable(['first_name', 'last_name'])
                            ->label(__('User')),
                        Select::make('status')
                            ->options([
                                'in_progress' => __('In Progress'),
                                'completed' => __('Completed'),
                            ])
                            ->required()
                            ->label(__('Status')),
                        DateTimePicker::make('completed_at')
                            ->label(__('Completion Date')),
                    ])
                    ->columns(2),

                // Edit form - show repeater for editing
                Section::make(__('Assessment Answers'))
                    ->schema([
                        Forms\Components\Repeater::make('answers')
                            ->relationship('answers')
                            ->schema([
                                Select::make('series_id')
                                    ->options(function () {
                                        return AssessmentSeries::query()
                                            ->orderBy('order')
                                            ->pluck('title', 'series_id');
                                    })
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('question_id', null);
                                    })
                                    ->label(__('Question Series')),
                                Select::make('question_id')
                                    ->options(function (callable $get) {
                                        $seriesId = $get('series_id');
                                        if (!$seriesId) return [];

                                        return AssessmentQuestion::query()
                                            ->where('series_id', $seriesId)
                                            ->orderBy('order')
                                            ->pluck('text', 'question_id');
                                    })
                                    ->required()
                                    ->reactive()
                                    ->label(__('Question')),
                                Forms\Components\TextInput::make('answer')
                                    ->required()
                                    ->label(__('Answer')),
                            ])
                            ->orderColumn('created_at')
                            ->defaultItems(0)
                            ->addActionLabel(__('Add Answer'))
                            ->label(__('Answers'))
                            ->collapsible(),
                    ])
                    ->visible(fn ($livewire) => !$livewire instanceof Pages\ViewUserAssessment),

                Section::make(__('Notes'))
                    ->schema([
                        ViewField::make('notes')
                            ->view('filament.resources.user-assessment.notes-view')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Medical Documents'))
                    ->schema([
                        ViewField::make('medical_documents')
                            ->view('filament.resources.user-assessment.medical-docs-view')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Medications'))
                    ->schema([
                        ViewField::make('drug_documents')
                            ->view('filament.resources.user-assessment.drug-docs-view')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('GPT Report'))
                    ->schema([
                        Textarea::make('gpt_response')
                            ->label(__('GPT Response'))
                            ->disabled()
                            ->columnSpanFull()
                            ->rows(10),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.first_name')
                    ->searchable()
                    ->sortable()
                    ->label(__('First Name')),
                Tables\Columns\TextColumn::make('user.last_name')
                    ->searchable()
                    ->sortable()
                    ->label(__('Last Name')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in_progress' => __('In Progress'),
                        'completed' => __('Completed'),
                        default => $state,
                    })
                    ->sortable()
                    ->label(__('Status')),
                Tables\Columns\TextColumn::make('answers_count')
                    ->counts('answers')
                    ->label(__('Number of Answers')),
                Tables\Columns\TextColumn::make('medical_documents_count')
                    ->counts('medicalDocuments')
                    ->label(__('Number of Medical Documents')),
                Tables\Columns\TextColumn::make('drug_documents_count')
                    ->counts('drugsDocuments')
                    ->label(__('Number of Medications')),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label(__('Completion Date')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label(__('Start Date')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'in_progress' => __('In Progress'),
                        'completed' => __('Completed'),
                    ])
                    ->label(__('Status')),
                Tables\Filters\Filter::make('completed')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('completed_at'))
                    ->label(__('Completed')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListUserAssessments::route('/'),
            'create' => Pages\CreateUserAssessment::route('/create'),
            'view' => Pages\ViewUserAssessment::route('/{record}'),
            'edit' => Pages\EditUserAssessment::route('/{record}/edit'),
        ];
    }
}
