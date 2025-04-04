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

    protected static ?string $title = 'ارزیابی‌ها';

    protected static ?string $modelLabel = 'ارزیابی';

    protected static ?string $pluralModelLabel = 'ارزیابی‌ها';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('عنوان'),
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
                        'in_progress' => 'در حال انجام',
                        'completed' => 'تکمیل شده',
                        default => $state,
                    })
                    ->sortable()
                    ->label('وضعیت'),
                Tables\Columns\TextColumn::make('answers_count')
                    ->counts('answers')
                    ->label('تعداد پاسخ‌ها'),
                Tables\Columns\TextColumn::make('medical_documents_count')
                    ->counts('medicalDocuments')
                    ->label('تعداد مدارک پزشکی'),
                Tables\Columns\TextColumn::make('drug_documents_count')
                    ->counts('drugsDocuments')
                    ->label('تعداد داروها'),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label('تاریخ تکمیل'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label('تاریخ ایجاد'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label('تاریخ بروزرسانی'),
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
