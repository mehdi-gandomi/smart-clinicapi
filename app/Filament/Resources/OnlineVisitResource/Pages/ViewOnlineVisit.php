<?php

namespace App\Filament\Resources\OnlineVisitResource\Pages;

use App\Filament\Resources\OnlineVisitResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\View;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ViewOnlineVisit extends ViewRecord
{
    protected static string $resource = OnlineVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('answer')
                ->label('پاسخ به بیمار')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->form([
                    Textarea::make('answer')
                        ->label('پاسخ')
                        ->required()
                        ->maxLength(1000)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'answer' => $data['answer'],
                        'status' => 'answered',
                        'answered_at' => now(),
                    ]);

                    Notification::make()
                        ->title('پاسخ با موفقیت ثبت شد')
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->status !== 'answered')
                ->requiresConfirmation()
                ->modalHeading('پاسخ به بیمار')
                ->modalSubmitActionLabel('ثبت پاسخ')
                ->modalCancelActionLabel('انصراف'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('اطلاعات ویزیت')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('نام بیمار'),
                        TextEntry::make('visit_type')
                            ->label('نوع ویزیت')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'medical_questions' => 'سوالات پزشکی',
                                'document_review' => 'بررسی مدارک',
                                'prescription_renewal' => 'تمدید نسخه',
                                default => $state,
                            }),
                        TextEntry::make('description')
                            ->label('توضیحات'),
                        TextEntry::make('status')
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
                        TextEntry::make('created_at')
                            ->label('تاریخ درخواست')
                            ->dateTime('Y-m-d H:i:s'),
                    ])
                    ->columns(2),
                Section::make('مدارک پزشکی')
                    ->schema([
                        View::make('medical-docs')
                            ->view('filament.resources.online-visit.medical-docs')
                            ->viewData([
                                'medicalDocs' => collect($this->record->medical_documents ?? []),
                            ]),
                    ]),
                Section::make('یادداشت صوتی')
                    ->schema([
                        View::make('voice-note')
                            ->view('filament.resources.online-visit.voice-note')
                            ->viewData([
                                'voiceNote' => $this->record->voice_note,
                            ]),
                    ]),
                Section::make('پاسخ پزشک')
                    ->visible(fn () => $this->record->status === 'answered')
                    ->schema([
                        TextEntry::make('answer')
                            ->label('پاسخ')
                            ->markdown()
                            ->columnSpanFull(),
                        TextEntry::make('answered_at')
                            ->label('تاریخ پاسخ')
                            ->dateTime('Y-m-d H:i:s'),
                    ]),
            ]);
    }
}
