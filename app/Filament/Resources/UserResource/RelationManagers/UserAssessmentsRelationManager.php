<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserAssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessments';

    protected static ?string $title = 'Assessments';

    protected static ?string $modelLabel = 'Assessment';

    protected static ?string $pluralModelLabel = 'Assessments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Title'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        default => $state,
                    })
                    ->sortable()
                    ->label('Status'),
                Tables\Columns\TextColumn::make('answers_count')
                    ->counts('answers')
                    ->label('Number of Answers'),
                Tables\Columns\TextColumn::make('medical_documents_count')
                    ->counts('medicalDocuments')
                    ->label('Number of Medical Documents'),
                Tables\Columns\TextColumn::make('drug_documents_count')
                    ->counts('drugsDocuments')
                    ->label('Number of Drugs'),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label('Completion Date'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label('Created At'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label('Updated At'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
