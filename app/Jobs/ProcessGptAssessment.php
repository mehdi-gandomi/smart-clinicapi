<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserAssessment;
use App\Models\AssessmentSeries;
use OpenAI\Factory;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ProcessGptAssessment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $assessment;
    protected $user;
    protected $customPrompt;

    /**
     * Create a new job instance.
     */
    public function __construct(UserAssessment $assessment, User $user, ?string $customPrompt = null)
    {
        $this->assessment = $assessment;
        $this->user = $user;
        $this->customPrompt = $customPrompt;
    }

    public function generateAssessmentPrompt(): string
    {
        $formattedText = __('Patient Personal Information:') . "\n";
        $formattedText .= "- نام و نام خانوادگی: {$this->user->first_name} {$this->user->last_name}\n";
        $formattedText .= "- سن: {$this->user->age}\n";
        $formattedText .= "- جنسیت: " . ($this->user->gender == 1 ? 'مرد' : 'زن') . "\n";
        $formattedText .= "- قد: {$this->user->height} cm\n";
        $formattedText .= "- وزن: {$this->user->weight} kg\n";
        $formattedText .= "- شغل: {$this->user->occupation}\n";
        $formattedText .= "- آدرس: {$this->user->address}\n";
        $formattedText .= "- بیمه اصلی: {$this->user->primary_insurance}\n";
        $formattedText .= "- بیمه تکمیلی: {$this->user->supplementary_insurance}\n\n";

        $formattedText .= "پاسخ‌های ارزیابی بیمار:\n\n";

        // Get all series with their questions
        $series = AssessmentSeries::with(['questions' => function ($query) {
            $query->orderBy('order');
        }])->orderBy('order')->get();

        // Get answers grouped by series
        $answers = $this->assessment->answers()
            ->with('question')
            ->get()
            ->groupBy('series_id');

        // Get notes grouped by series
        $notes = $this->assessment->notes()
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



        $formattedText .= "\nPlease write a comprehensive analysis of the patient's personal information and assessment answers. Include:\n";
        $formattedText .= "1. Patient introduction and personal information analysis\n";
        $formattedText .= "2. Risk factors identification\n";
        $formattedText .= "3. Lifestyle status evaluation\n";
        $formattedText .= "4. Symptom analysis (typical vs atypical in percentages)\n";
        $formattedText .= "5. Pretest probability of CAD\n";
        $formattedText .= "6. Differential diagnoses\n";
        $formattedText .= "7. Economic status assessment and willingness for diagnostic tests\n";
        $formattedText .= "\nPlease write this analysis in English, but keep patient's personal information in Original language.";

        return $formattedText;
    }

    protected function getImageBase64(string $path): ?string
    {
        try {
            $fullPath = Storage::path($path);
            $imageData = file_get_contents($fullPath);
            return base64_encode($imageData);
        } catch (\Exception $e) {
            \Log::error('Error reading image: ' . $e->getMessage());
            return null;
        }
    }



    protected function processDocuments(): string
    {
        try {
            $medicalDocs = $this->assessment->medicalDocuments()->get();
            $drugDocs = $this->assessment->drugsDocuments()->get();

            $allImages = [];

                // Prepare personal info array
                $personalInfo = [
                    "- نام و نام خانوادگی: {$this->user->first_name} {$this->user->last_name}",
                    "- سن: {$this->user->age}",
                    "- جنسیت: " . ($this->user->gender == 1 ? 'مرد' : 'زن'),
                    "- قد: {$this->user->height} cm",
                    "- وزن: {$this->user->weight} kg",
                    "- شغل: {$this->user->occupation}",
                    "- آدرس: {$this->user->address}",
                    "- بیمه اصلی: {$this->user->primary_insurance}",
                    "- بیمه تکمیلی: {$this->user->supplementary_insurance}",
                ];
                $formattedText = "Patient Information:\n" . implode("\n", $personalInfo);

                $formattedText.="\n\nPlease analyze these medical documents and prescriptions, providing a detailed analysis that includes:\n1. Summary of medical test results and their significance\n2. List of all medications, their dosages, and purposes\n3. Potential drug interactions or concerns\n4. Recommendations for additional tests or monitoring\n5. Overall assessment of the patient's medical documentation like Blood test,Echocardiography,ECG,Sonography and more";

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
                \Log::info('GPT API Request Data', $requestData);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', $requestData);


                if ($response->successful()) {
                    $content = $response->json('choices.0.message.content');
                    if ($content) {
                        // Log the successful response content length
                        \Log::info('GPT API response content', ['response'=>$content]);

                        $this->assessment->update([
                            'documents_prompt' => $formattedText,
                            'documents_response' => $content,
                            'gpt_error' => null,
                        ]);
                        return true;
                    }
                }

                // Log detailed error information
                \Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'assessment_id' => $this->assessment->id,
                    'user_id' => $this->assessment->user_id
                ]);

                $this->assessment->update([
                    'gpt_error' => 'Failed to get response from OpenAI API\n'.$response->body(),
                ]);
                return false;
            }

            Log::error('No images found to analyze', [
                'assessment_id' => $this->assessment->id,
                'user_id' => $this->assessment->user_id
            ]);
            $this->assessment->update([
                'gpt_error' => 'No images found to analyze',
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Document processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'assessment_id' => $this->assessment->id,
                'user_id' => $this->assessment->user_id
            ]);
            $this->assessment->update([
                'gpt_error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    protected function processAssessment(){
        $prompt=$this->generateAssessmentPrompt();
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
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => 4000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if ($content) {
                    $this->assessment->update([
                        'assessment_prompt' => $prompt,
                        'assessment_response' => $content,
                        'gpt_error' => null,
                    ]);
                    return true;
                }
            }

            \Log::error('OpenAI API Error: ' . $response->body());
            $this->assessment->update([
                'gpt_error' => 'Failed to get response from OpenAI API',
            ]);
            return false;
        } catch (\Exception $e) {
            \Log::error('Assessment processing error: ' . $e->getMessage());
            $this->assessment->update([
                'gpt_error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    protected function sendToGPT(string $prompt, string $type): ?string
    {
        try {
            $client = (new Factory)
                ->withApiKey(config('services.openai.api_key'))
                ->withHttpClient(new \GuzzleHttp\Client([
                    'verify' => false,
                    'timeout' => 30,
                    'connect_timeout' => 10,
                    'http_errors' => false,
                    'headers' => [
                        'User-Agent' => 'SmartClinic/1.0',
                        'Accept' => 'application/json',
                    ]
                ]))
                ->make();

            $response = $client->chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $type === 'documents'
                            ? 'You are a medical assistant analyzing medical documents and prescriptions.'
                            : 'You are a medical assistant analyzing patient assessment data and personal information.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 4000,
                'temperature' => 0.7,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
            ]);

            return $response->choices[0]->message->content;

        } catch (\Exception $e) {
            \Log::error('OpenAI API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {


        // Process documents if they exist
        if ($this->assessment->medicalDocuments()->count() > 0 || $this->assessment->drugsDocuments()->count() > 0) {
            $this->processDocuments();
        }

        // Process assessment
        $this->processAssessment();


    }
}
