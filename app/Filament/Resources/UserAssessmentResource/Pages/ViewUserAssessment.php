<?php

namespace App\Filament\Resources\UserAssessmentResource\Pages;

use App\Filament\Resources\UserAssessmentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Actions\Action;
use App\Jobs\ProcessGptAssessment;
use App\Models\AssessmentSeries;
use App\Settings\PromptSettings;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ViewUserAssessment extends ViewRecord
{
    protected static string $resource = UserAssessmentResource::class;

    public ?string $assessmentPrompt = null;
    public ?string $documentsPrompt = null;
    public ?string $fullPrompt = null;
    public ?string $gptResponse = null;
    public ?string $documentsPromptAdditional = null;
    public ?string $assessmentPromptAdditional = null;
    public ?string $fullPromptAdditional = null;
    public function generateFullPrompt(): string
    {
        $formattedText = __('Patient Personal Information:') . "\n";
        $formattedText .= "- نام و نام خانوادگی: {$this->record->user->first_name} {$this->record->user->last_name}\n";
        $formattedText .= "- سن: {$this->record->user->age}\n";
        $formattedText .= "- جنسیت: " . ($this->record->user->gender == 1 ? 'مرد' : 'زن') . "\n";
        $formattedText .= "- قد: {$this->record->user->height} cm\n";
        $formattedText .= "- وزن: {$this->record->user->weight} kg\n";
        $formattedText .= "- شغل: {$this->record->user->occupation}\n";
        $formattedText .= "- آدرس: {$this->record->user->address}\n";
        $formattedText .= "- بیمه اصلی: {$this->record->user->primary_insurance}\n";
        $formattedText .= "- بیمه تکمیلی: {$this->record->user->supplementary_insurance}\n\n";

        $formattedText .= "پاسخ‌های ارزیابی بیمار:\n\n";

        // Get all series with their questions
        $series = AssessmentSeries::with(['questions' => function ($query) {
            $query->orderBy('order');
        }])->orderBy('order')->get();

        // Get answers grouped by series
        $answers = $this->record->answers()
            ->with('question')
            ->get()
            ->groupBy('series_id');

        // Get notes grouped by series
        $notes = $this->record->notes()
            ->get()
            ->keyBy('series_id');



        // Group answers by series
        $groupedAnswers = [];
        foreach ($series as $s) {
            $seriesAnswers = [];
            $seriesAnswersCollection = $answers->get($s->series_id, collect());

            foreach ($s->questions as $question) {
                $answer = $seriesAnswersCollection->first(function($a) use ($question) {
                    return $a->question_id === $question->question_id;
                });

                if ($answer) {
                    $seriesAnswers[] = [
                        'question' => $question->text,
                        'answer' => $answer->answer
                    ];
                }
            }

            if (!empty($seriesAnswers)) {
                $groupedAnswers[] = [
                    'series_title' => $s->title,
                    'answers' => $seriesAnswers
                ];
            }
        }

        // Add answers to formatted text
        foreach ($groupedAnswers as $group) {
            $formattedText .= "{$group['series_title']}:\n";
            foreach ($group['answers'] as $qa) {
                $formattedText .= "- {$qa['question']}: {$qa['answer']}\n";
            }
            $formattedText .= "\n";
        }

        // Add notes if they exist
        if ($notes->isNotEmpty()) {
            $formattedText .= "یادداشت‌ها:\n";
            foreach ($notes as $seriesId => $note) {
                $seriesTitle = $series->firstWhere('series_id', $seriesId)->title;
                $formattedText .= "- {$seriesTitle}: {$note->notes}\n";
            }
            $formattedText .= "\n";
        }



        $formattedText .= $this->fullPromptAdditional;

        return $formattedText;
    }

    public function generateAssessmentPrompt(): string
    {
        $formattedText = __('Patient Personal Information:') . "\n";
        $formattedText .= "- نام و نام خانوادگی: {$this->record->user->first_name} {$this->record->user->last_name}\n";
        $formattedText .= "- سن: {$this->record->user->age}\n";
        $formattedText .= "- جنسیت: " . ($this->record->user->gender == 1 ? 'مرد' : 'زن') . "\n";
        $formattedText .= "- قد: {$this->record->user->height} cm\n";
        $formattedText .= "- وزن: {$this->record->user->weight} kg\n";
        $formattedText .= "- شغل: {$this->record->user->occupation}\n";
        $formattedText .= "- آدرس: {$this->record->user->address}\n";
        $formattedText .= "- بیمه اصلی: {$this->record->user->primary_insurance}\n";
        $formattedText .= "- بیمه تکمیلی: {$this->record->user->supplementary_insurance}\n\n";

        $formattedText .= "پاسخ‌های ارزیابی بیمار:\n\n";

        // Get all series with their questions
        $series = AssessmentSeries::with(['questions' => function ($query) {
            $query->orderBy('order');
        }])->orderBy('order')->get();

        // Get answers grouped by series
        $answers = $this->record->answers()
            ->with('question')
            ->get()
            ->groupBy('series_id');

        // Get notes grouped by series
        $notes = $this->record->notes()
            ->get()
            ->keyBy('series_id');



        // Group answers by series
        $groupedAnswers = [];
        foreach ($series as $s) {
            $seriesAnswers = [];
            $seriesAnswersCollection = $answers->get($s->series_id, collect());

            foreach ($s->questions as $question) {
                $answer = $seriesAnswersCollection->first(function($a) use ($question) {
                    return $a->question_id === $question->question_id;
                });

                if ($answer) {
                    $seriesAnswers[] = [
                        'question' => $question->text,
                        'answer' => $answer->answer
                    ];
                }
            }

            if (!empty($seriesAnswers)) {
                $groupedAnswers[] = [
                    'series_title' => $s->title,
                    'answers' => $seriesAnswers
                ];
            }
        }

        // Add answers to formatted text
        foreach ($groupedAnswers as $group) {
            $formattedText .= "{$group['series_title']}:\n";
            foreach ($group['answers'] as $qa) {
                $formattedText .= "- {$qa['question']}: {$qa['answer']}\n";
            }
            $formattedText .= "\n";
        }

        // Add notes if they exist
        if ($notes->isNotEmpty()) {
            $formattedText .= "یادداشت‌ها:\n";
            foreach ($notes as $seriesId => $note) {
                $seriesTitle = $series->firstWhere('series_id', $seriesId)->title;
                $formattedText .= "- {$seriesTitle}: {$note->notes}\n";
            }
            $formattedText .= "\n";
        }



        $formattedText .= $this->assessmentPromptAdditional;

        return $formattedText;
    }

    public function generateDocsPrompt(){
        // Prepare personal info array
        $personalInfo = [
            "- نام و نام خانوادگی: {$this->record->user->first_name} {$this->record->user->last_name}",
            "- سن: {$this->record->user->age}",
            "- جنسیت: " . ($this->record->user->gender == 1 ? 'مرد' : 'زن'),
            "- قد: {$this->record->user->height} cm",
            "- وزن: {$this->record->user->weight} kg",
            "- شغل: {$this->record->user->occupation}",
            "- آدرس: {$this->record->user->address}",
            "- بیمه اصلی: {$this->record->user->primary_insurance}",
            "- بیمه تکمیلی: {$this->record->user->supplementary_insurance}",
        ];
        $formattedText = "Patient Information:\n" . implode("\n", $personalInfo);

        $formattedText.="\n\n".$this->documentsPromptAdditional;
        return $formattedText;
    }
    protected function getPromptSettings(): PromptSettings
    {
        return app(PromptSettings::class);
    }
    public function mount($record): void
    {
        parent::mount($record);
        $settings = $this->getPromptSettings();
        $this->documentsPromptAdditional = $settings->documents_prompt;
        $this->assessmentPromptAdditional = $settings->assessment_prompt;
        $this->fullPromptAdditional = $settings->full_prompt;
        $this->assessmentPrompt = $this->generateAssessmentPrompt();
        $this->documentsPrompt = $this->generateDocsPrompt();
        $this->fullPrompt=$this->generateFullPrompt();
    }

    protected function getHeaderActions(): array
    {
        return [
            // ActionGroup::make([

            // ])
            // ->label(__('GPT Analysis'))
            // ->icon('heroicon-o-cpu-chip')
            // ->color('primary'),
            Action::make('editAssessmentPrompt')
                    ->label(__('Assessment Analysis'))
                    ->color('success')
                    ->icon('heroicon-o-user')
                    ->modalWidth('7xl')
                    ->modalHeading(__('Assessment and Personal Info Analysis'))
                    ->modalDescription(__('Review and edit the assessment prompt before sending to GPT'))
                    ->form([
                        Forms\Components\Textarea::make('assessmentPrompt')
                            ->label('')
                            ->default(fn () => $this->assessmentPrompt)
                            ->rows(20)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->modalSubmitActionLabel(__('Send to GPT'))
                    ->modalCancelActionLabel(__('Cancel'))
                    ->action(function (array $data) {
                        if ($this->processAssessment($data['assessmentPrompt'])) {
                            $this->record->refresh();

                            // Show response in new modal
                            Action::make('showAssessmentResponse')
                                ->label(__('Assessment Analysis Result'))
                                ->modalContent(view('filament.components.gpt-response', [
                                    'response' => $this->record->assessment_response,
                                    'error' => null,
                                ]))
                                ->modalWidth('7xl')
                                ->modalHeading(__('Assessment Analysis Result'))
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel(__('Close'))
                                ->call();

                            Notification::make()
                                ->success()
                                ->title(__('Assessment analysis completed'))
                                ->send();
                        } else {
                            // Show error in modal
                            Action::make('showAssessmentError')
                                ->label(__('Assessment Analysis Error'))
                                ->modalContent(view('filament.components.gpt-response', [
                                    'response' => null,
                                    'error' => __('Failed to process assessment'),
                                ]))
                                ->modalWidth('md')
                                ->modalHeading(__('Error'))
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel(__('Close'))
                                ->call();

                            Notification::make()
                                ->danger()
                                ->title(__('Failed to process assessment'))
                                ->send();
                        }
                    }),

                Action::make('editDocumentsPrompt')
                    ->label(__('Documents Analysis'))
                    ->color('warning')
                    ->icon('heroicon-o-document-text')
                    ->modalWidth('7xl')
                    ->modalHeading(__('Medical Documents and Drugs Analysis'))
                    ->modalDescription(__('Review medical documents and send to GPT for analysis'))
                    ->visible(fn () => $this->record->medicalDocuments()->count() > 0 || $this->record->drugsDocuments()->count() > 0)
                    ->form([
                        Forms\Components\Textarea::make('documentsPrompt')
                            ->label('')
                            ->default(fn () => $this->documentsPrompt)
                            ->rows(20)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->modalSubmitActionLabel(__('Send to GPT'))
                    ->modalCancelActionLabel(__('Cancel'))
                    ->action(function (array $data) {
                        if ($this->processDocuments()) {
                            $this->record->refresh();

                            // Show response in new modal
                            Action::make('showDocumentsResponse')
                                ->label(__('Documents Analysis Result'))
                                ->modalContent(view('filament.components.gpt-response', [
                                    'response' => $this->record->documents_response,
                                    'error' => null,
                                ]))
                                ->modalWidth('7xl')
                                ->modalHeading(__('Documents Analysis Result'))
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel(__('Close'))
                                ->call();

                            Notification::make()
                                ->success()
                                ->title(__('Documents analysis completed'))
                                ->send();
                        } else {
                            // Show error in modal
                            Action::make('showDocumentsError')
                                ->label(__('Documents Analysis Error'))
                                ->modalContent(view('filament.components.gpt-response', [
                                    'response' => null,
                                    'error' => __('Failed to process documents'),
                                ]))
                                ->modalWidth('md')
                                ->modalHeading(__('Error'))
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel(__('Close'))
                                ->call();

                            Notification::make()
                                ->danger()
                                ->title(__('Failed to process documents'))
                                ->send();
                        }
                    }),


                    Action::make('editFullPrompt')
                    ->label(__('Full Analysis'))
                    ->color('warning')
                    ->icon('heroicon-o-document-text')
                    ->modalWidth('7xl')
                    ->modalHeading(__('Medical Documents and Assessment Analysis'))
                    ->modalDescription(__('Review medical documents and assessment and send to GPT for analysis'))
                    // ->visible(fn () => $this->record->medicalDocuments()->count() > 0 || $this->record->drugsDocuments()->count() > 0)
                    ->form([
                        Forms\Components\Textarea::make('fullPrompt')
                            ->label('')
                            ->default(fn () => $this->fullPrompt)
                            ->rows(20)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->modalSubmitActionLabel(__('Send to GPT'))
                    ->modalCancelActionLabel(__('Cancel'))
                    ->action(function (array $data) {
                        if ($this->processFullPrompt()) {
                            $this->record->refresh();
                        } else {

                            Notification::make()
                                ->danger()
                                ->title(__('Failed to process documents'))
                                ->send();
                        }
                    }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('Assessment Information'))
                    ->schema([
                        TextEntry::make('user.first_name')
                            ->label(__('First Name')),
                        TextEntry::make('user.last_name')
                            ->label(__('Last Name')),
                        TextEntry::make('status')
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
                            ->label(__('Status')),
                        TextEntry::make('completed_at')
                            ->dateTime('Y-m-d H:i:s')
                            ->label(__('Completion Date')),
                    ])
                    ->columns(2),

                Section::make(__('Answers and Notes'))
                    ->schema([
                        ViewEntry::make('answers')
                            ->view('filament.resources.user-assessment.answers-view')
                            ->visible(fn ($record) => $record->answers()->count() > 0),
                    ])
                    ->collapsible(),

                Section::make(__('Medical Documents'))
                    ->schema([
                        ViewEntry::make('medicalDocuments')
                            ->view('filament.resources.user-assessment.medical-docs-view')
                            ->visible(fn ($record) => $record->medicalDocuments()->count() > 0),
                    ])
                    ->collapsible(),

                Section::make(__('Medications'))
                    ->schema([
                        ViewEntry::make('drugsDocuments')
                            ->view('filament.resources.user-assessment.drug-docs-view')
                            ->visible(fn ($record) => $record->drugsDocuments()->count() > 0),
                    ])
                    ->collapsible(),

                Section::make(__('Assistant Report'))
                    ->schema([
                        TextEntry::make('gpt_error')
                            ->label(__('GPT Error'))
                            ->color('danger')
                            ->visible(fn ($record) => !empty($record->gpt_error))
                            ->columnSpanFull(),

                        Section::make(__('Full Analysis'))
                            ->schema([
                                TextEntry::make('full_response')
                                    ->markdown()
                                    ->label(__('Full Analysis Result'))
                                    ->columnSpanFull(),

                                Section::make(__('Full Analysis Prompt'))
                                    ->schema([
                                        TextEntry::make('full_prompt')
                                            ->html()
                                            ->formatStateUsing(fn ($state) => nl2br(e($state)))
                                            ->label(__('Full Prompt'))
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsed(),
                            ])
                            ->collapsible()
                            ->visible(fn ($record) => !empty($record->full_response) || !empty($record->full_prompt)),

                        Section::make(__('Documents Analysis'))
                            ->schema([
                                TextEntry::make('documents_response')
                                    ->markdown()
                                    ->label(__('Documents Analysis Result'))
                                    ->columnSpanFull(),

                                Section::make(__('Documents Analysis Prompt'))
                                    ->schema([
                                        TextEntry::make('documents_prompt')
                                            ->html()
                                            ->formatStateUsing(fn ($state) => nl2br(e($state)))
                                            ->label(__('Documents Prompt'))
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsed(),
                            ])
                            ->collapsible()
                            ->visible(fn ($record) => !empty($record->documents_response) || !empty($record->documents_prompt)),

                        Section::make(__('Assessment Analysis'))
                            ->schema([
                                TextEntry::make('assessment_response')
                                    ->markdown()
                                    ->label(__('Assessment Analysis Result'))
                                    ->columnSpanFull(),

                                Section::make(__('Assessment Analysis Prompt'))
                                    ->schema([
                                        TextEntry::make('assessment_prompt')
                                            ->html()
                                            ->formatStateUsing(fn ($state) => nl2br(e($state)))
                                            ->label(__('Assessment Prompt'))
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsed(),
                            ])
                            ->collapsible()
                            ->visible(fn ($record) => !empty($record->assessment_response) || !empty($record->assessment_prompt)),
                    ])
                    ->collapsible(),
            ]);
    }

    protected function processAssessment(?string $customPrompt = null): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a medical assistant analyzing patient assessment data and personal information.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $customPrompt ?? $this->assessmentPrompt,
                    ],
                ],
                'max_tokens' => 4000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if ($content) {
                    $this->record->update([
                        'assessment_prompt' => $customPrompt ?? $this->assessmentPrompt,
                        'assessment_response' => $content,
                        'gpt_error' => null,
                    ]);
                    return true;
                }
            }

            Log::error('OpenAI API Error: ' . $response->body());
            $this->record->update([
                'gpt_error' => 'Failed to get response from OpenAI API',
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Assessment processing error: ' . $e->getMessage());
            $this->record->update([
                'gpt_error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function getImageBase64(string $path): ?string
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            $imageData = file_get_contents($fullPath);
            return base64_encode($imageData);
        } catch (\Exception $e) {
            Log::error('Error reading image: ' . $e->getMessage());
            return null;
        }
    }

    protected function processDocuments(): bool
    {
        try {
            $medicalDocs = $this->record->medicalDocuments()->get();
            $drugDocs = $this->record->drugsDocuments()->get();

            $allImages = [];
            $formattedText = $this->documentsPrompt;

            // Process medical documents
            if ($medicalDocs->isNotEmpty()) {

                foreach ($medicalDocs as $doc) {


                    foreach ($doc->files as $file) {
                        $imagePath = Storage::disk('public')->path($file['path']);
                        $imageData = file_get_contents($imagePath);
                        $base64Image = base64_encode($imageData);
                        $mimeType = $file['mime_type'] ?? 'image/jpeg';
                        $allImages[] = [
                            'type' => 'image_url',
                            'image_url' => [
                                'url'=>"data:{$mimeType};base64,{$base64Image}"
                            ]
                        ];
                    }
                }
            }

            // Process drug documents
            if ($drugDocs->isNotEmpty()) {

                foreach ($drugDocs as $doc) {

                    foreach ($doc->files as $file) {
                        $imagePath = Storage::disk('public')->path($file['path']);
                        $imageData = file_get_contents($imagePath);
                        $base64Image = base64_encode($imageData);
                        $mimeType = $file['mime_type'] ?? 'image/jpeg';



                        $allImages[] = [
                            'type' => 'image_url',
                            'image_url' => [
                                'url'=>"data:{$mimeType};base64,{$base64Image}"
                            ]
                        ];
                    }
                }
            }

            if (!empty($allImages)) {

                $requestData = [
                    'model' => 'gpt-4o',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a medical assistant analyzing medical documents, test results, and prescriptions. Please provide a comprehensive analysis of these medical documents in the context of the patient information provided.',
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $formattedText,
                                ],
                                ...$allImages
                            ],
                        ],
                    ],
                    'max_tokens' => 4000,
                ];

                // Log the complete request data (excluding sensitive information)
                Log::info('GPT API Request Data', $requestData);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', $requestData);


                if ($response->successful()) {
                    $content = $response->json('choices.0.message.content');
                    if ($content) {
                        // Log the successful response content length
                        Log::info('GPT API response content', ['response'=>$content]);

                        $this->record->update([
                            'documents_prompt' => $formattedText,
                            'documents_response' => $content,
                            'gpt_error' => null,
                        ]);
                        return true;
                    }
                }

                // Log detailed error information
                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'assessment_id' => $this->record->id,
                    'user_id' => $this->record->user_id
                ]);

                $this->record->update([
                    'gpt_error' => 'Failed to get response from OpenAI API\n'.$response->body(),
                ]);
                return false;
            }

            Log::error('No images found to analyze', [
                'assessment_id' => $this->record->id,
                'user_id' => $this->record->user_id
            ]);
            $this->record->update([
                'gpt_error' => 'No images found to analyze',
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Document processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'assessment_id' => $this->record->id,
                'user_id' => $this->record->user_id
            ]);
            $this->record->update([
                'gpt_error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function processFullPrompt(): bool
    {
        try {
            $medicalDocs = $this->record->medicalDocuments()->get();
            $drugDocs = $this->record->drugsDocuments()->get();

            $allImages = [];
            $formattedText = $this->fullPrompt;

            // Process medical documents
            if ($medicalDocs->isNotEmpty()) {

                foreach ($medicalDocs as $doc) {


                    foreach ($doc->files as $file) {
                        $imagePath = Storage::disk('public')->path($file['path']);
                        $imageData = file_get_contents($imagePath);
                        $base64Image = base64_encode($imageData);
                        $mimeType = $file['mime_type'] ?? 'image/jpeg';
                        $allImages[] = [
                            'type' => 'image_url',
                            'image_url' => [
                                'url'=>"data:{$mimeType};base64,{$base64Image}"
                            ]
                        ];
                    }
                }
            }

            // Process drug documents
            if ($drugDocs->isNotEmpty()) {

                foreach ($drugDocs as $doc) {

                    foreach ($doc->files as $file) {
                        $imagePath = Storage::disk('public')->path($file['path']);
                        $imageData = file_get_contents($imagePath);
                        $base64Image = base64_encode($imageData);
                        $mimeType = $file['mime_type'] ?? 'image/jpeg';



                        $allImages[] = [
                            'type' => 'image_url',
                            'image_url' => [
                                'url'=>"data:{$mimeType};base64,{$base64Image}"
                            ]
                        ];
                    }
                }
            }

            if (!empty($allImages)) {

                $requestData = [
                    'model' => 'gpt-4o',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a medical assistant analyzing medical documents, test results, and prescriptions. Please provide a comprehensive analysis of these medical documents in the context of the patient information provided.',
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $formattedText,
                                ],
                                ...$allImages
                            ],
                        ],
                    ],
                    'max_tokens' => 4000,
                ];

                // Log the complete request data (excluding sensitive information)
                Log::info('GPT API Request Data', $requestData);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', $requestData);


                if ($response->successful()) {
                    $content = $response->json('choices.0.message.content');
                    if ($content) {
                        // Log the successful response content length
                        Log::info('GPT API response content', ['response'=>$content]);

                        $this->record->update([
                            'full_prompt' => $formattedText,
                            'full_response' => $content,
                            'gpt_error' => null,
                        ]);
                        return true;
                    }
                }

                // Log detailed error information
                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'assessment_id' => $this->record->id,
                    'user_id' => $this->record->user_id
                ]);

                $this->record->update([
                    'gpt_error' => 'Failed to get response from OpenAI API\n'.$response->body(),
                ]);
                return false;
            }

            Log::error('No images found to analyze', [
                'assessment_id' => $this->record->id,
                'user_id' => $this->record->user_id
            ]);
            $this->record->update([
                'gpt_error' => 'No images found to analyze',
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Document processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'assessment_id' => $this->record->id,
                'user_id' => $this->record->user_id
            ]);
            $this->record->update([
                'gpt_error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
