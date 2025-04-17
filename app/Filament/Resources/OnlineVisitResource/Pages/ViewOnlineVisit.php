<?php

namespace App\Filament\Resources\OnlineVisitResource\Pages;

use App\Filament\Resources\OnlineVisitResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Forms\Components\VoiceRecorder;

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
                    Select::make('answer_type')
                        ->label('نوع پاسخ')
                        ->options([
                            'text' => 'پاسخ متنی',
                            'voice' => 'پاسخ صوتی',
                        ])
                        ->required()
                        ->live()
                        ->default('text'),

                    Textarea::make('answer')
                        ->label('پاسخ متنی')
                        ->maxLength(1000)
                        ->columnSpanFull()
                        ->required(fn ($get) => $get('answer_type') === 'text')
                        ->visible(fn ($get) => $get('answer_type') === 'text'),

                    VoiceRecorder::make('voice_answer')
                        ->label('پاسخ صوتی')
                        ->columnSpanFull()
                        ->required(fn ($get) => $get('answer_type') === 'voice')
                        ->visible(fn ($get) => $get('answer_type') === 'voice'),

                    // TextInput::make('voice_answer_duration')
                    //     ->label('مدت زمان پاسخ صوتی')
                    //     ->placeholder('مثال: 2:30')
                    //     ->required(fn ($get) => $get('answer_type') === 'voice')
                    //     ->visible(fn ($get) => $get('answer_type') === 'voice'),
                ])
                ->action(function (array $data) {
                    try {
                        $updateData = [
                            'status' => 'answered',
                            'answered_at' => now(),
                        ];
                        dd($data);
                        if ($data['answer_type'] === 'text') {
                            $updateData['answer'] = $data['answer'];
                            $updateData['voice_answer'] = null;
                            $updateData['voice_answer_duration'] = null;
                        } else {
                            // Handle voice answer file upload
                            if (isset($data['voice_answer']) && $data['voice_answer'] instanceof \Illuminate\Http\UploadedFile) {
                                $file = $data['voice_answer'];
                                $path = $file->store('online-visits/voice-answers', 'public');
                                $updateData['voice_answer'] = $path;

                                // Calculate duration using getID3
                                $duration = $this->calculateAudioDuration($file);
                                $updateData['voice_answer_duration'] = $duration;
                            } else if (isset($data['voice_answer']) && is_array($data['voice_answer'])) {
                                // Handle the case where voice_answer is an array (from our custom component)
                                $file = $data['voice_answer'][0] ?? null;
                                if ($file) {
                                    $path = $file->store('online-visits/voice-answers', 'public');
                                    $updateData['voice_answer'] = $path;

                                    // Get duration from the hidden input
                                    $updateData['voice_answer_duration'] = $data['voice_answer_duration'] ?? null;
                                } else {
                                    throw new \Exception('لطفا یک پاسخ صوتی ضبط کنید.');
                                }
                            } else {
                                throw new \Exception('لطفا یک پاسخ صوتی ضبط کنید.');
                            }

                            $updateData['answer'] = null;
                        }

                        $this->record->update($updateData);

                        Notification::make()
                            ->title('پاسخ با موفقیت ثبت شد')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('خطا در ثبت پاسخ')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
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
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'medical_questions' => 'سوالات پزشکی',
                                'document_review' => 'بررسی مدارک',
                                'prescription_renewal' => 'تمدید نسخه',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'medical_questions' => 'primary',
                                'document_review' => 'success',
                                'prescription_renewal' => 'warning',
                                default => 'secondary',
                            }),
                        TextEntry::make('status')
                            ->label('وضعیت')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'در انتظار بررسی',
                                'in_progress' => 'در حال بررسی',
                                'answered' => 'پاسخ داده شده',
                                'cancelled' => 'لغو شده',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'in_progress' => 'primary',
                                'answered' => 'success',
                                'cancelled' => 'danger',
                                default => 'secondary',
                            }),
                        TextEntry::make('created_at')
                            ->label('تاریخ ایجاد')
                            ->dateTime('Y-m-d H:i:s'),
                        TextEntry::make('answered_at')
                            ->label('تاریخ پاسخ')
                            ->dateTime('Y-m-d H:i:s'),
                    ])
                    ->columns(3),

                Section::make('توضیحات')
                    ->schema([
                        TextEntry::make('description')
                            ->label('توضیحات بیمار')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Section::make('مدارک پزشکی')
                    ->schema([
                        ViewEntry::make('medical_documents')
                            ->view('filament.resources.online-visit.medical-docs')
                            ->columnSpanFull(),
                    ]),

                Section::make('پاسخ پزشک')
                    ->schema([
                        TextEntry::make('answer')
                            ->label('پاسخ متنی')
                            ->markdown()
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->answer)),
                        ViewEntry::make('voice_answer')
                            ->view('filament.resources.online-visit.voice-answer')
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->voice_answer)),
                    ]),
            ]);
    }

    /**
     * Calculate the duration of an audio file in seconds using getID3
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return int|null
     */
    protected function calculateAudioDuration($file)
    {
        try {
            // Get the file path
            $filePath = $file->getRealPath();

            // Use getID3 to analyze the file
            $getID3 = new \getID3();
            $fileInfo = $getID3->analyze($filePath);

            // Check if duration information is available
            if (isset($fileInfo['playtime_seconds'])) {
                return (int) $fileInfo['playtime_seconds'];
            }

            // If duration is not available, return null
            return null;
        } catch (\Exception $e) {
            // Log the error but don't break the application
            Log::error('Error calculating audio duration: ' . $e->getMessage());
            return null;
        }
    }
}
