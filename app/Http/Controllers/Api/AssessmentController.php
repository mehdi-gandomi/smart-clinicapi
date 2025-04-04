<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAssessment;
use App\Models\UserAssessmentAnswer;
use App\Models\UserAssessmentDocument;
use App\Models\UserAssessmentNote;
use App\Models\AssessmentSeries;
use App\Models\AssessmentQuestion;
use OpenAI\Factory;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\User;
use App\Jobs\ProcessGptAssessment;

class AssessmentController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Save answers for assessment
     */
    public function saveAnswers(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'answers' => 'required|array',
            'notes' => 'nullable|array',
            'status' => 'nullable|string|in:in_progress,completed'
        ]);

        $user = User::find(Auth::id());
        $status = $validated['status'] ?? 'completed';

        try {
            DB::beginTransaction();

            // Find or create a user assessment record for this user
            $assessment = UserAssessment::create(
                [
                    'user_id' => $user->id,
                    'status' => $status,
                    'completed_at' => $status === 'completed' ? now() : null
                ]
            );



            // Save the answers
            $answersToInsert = [];

            foreach ($validated['answers'] as $seriesId => $questionIds) {
                foreach ($questionIds as $questionId) {
                    $answersToInsert[] = [
                        'user_assessment_id' => $assessment->id,
                        'series_id' => $seriesId,
                        'question_id' => $questionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Bulk insert all answers
            if (!empty($answersToInsert)) {
                UserAssessmentAnswer::insert($answersToInsert);
            }

            // Save the notes if provided
            if (isset($validated['notes']) && is_array($validated['notes'])) {
                foreach ($validated['notes'] as $seriesId => $note) {
                    if (!empty($note)) {
                        UserAssessmentNote::create(
                            [
                                'user_assessment_id' => $assessment->id,
                                'series_id' => $seriesId,
                                'notes' => $note
                            ]
                        );
                    }
                }
            }

            DB::commit();

            if($user->assessments->count() == 1){
                $user->update(['done_assessment' => 1,'done_assessment_at' => now()]);
            }



            return response()->json([
                'status' => 'success',
                'message' => 'Assessment answers saved successfully',
                'data' => [
                    'user_assessment_id' => $assessment->id,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save assessment answers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function askGpt(Request $request)
    {
        $user = User::find(Auth::id());
        $assessment = $user->assessments()->latest()->first();

        // Create formatted text starting with user info
        $formattedText = "اطلاعات شخصی بیمار:\n";
        $formattedText .= "- نام و نام خانوادگی: {$user->first_name} {$user->last_name}\n";
        $formattedText .= "- سن: {$user->age}\n";
        $formattedText .= "- جنسیت: {$user->gender}\n";
        $formattedText .= "- قد: {$user->height} سانتی‌متر\n";
        $formattedText .= "- وزن: {$user->weight} کیلوگرم\n";
        $formattedText .= "- شغل: {$user->occupation}\n";
        $formattedText .= "- آدرس: {$user->address}\n";
        $formattedText .= "- بیمه اصلی: {$user->primary_insurance}\n";
        $formattedText .= "- بیمه تکمیلی: {$user->supplementary_insurance}\n\n";

        $formattedText .= "پاسخ‌های ارزیابی بیمار:\n\n";

        // Get all series with their questions
        $series = AssessmentSeries::with(['questions' => function ($query) {
            $query->orderBy('order');
        }])->orderBy('order')->get();

        // Get answers grouped by series
        $answers = $assessment->answers()
            ->with('question')
            ->get()
            ->groupBy('series_id');

        // Get notes grouped by series
        $notes = $assessment->notes()
            ->get()
            ->keyBy('series_id');

        // Get medical documents and drugs
        $medicalDocs = $assessment->medicalDocuments()

            ->get();

        $drugDocs = $assessment->drugsDocuments()

            ->get();

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

        // Create formatted text
        $formattedText .= "پاسخ‌های ارزیابی:\n\n";
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

        // Add medical documents information
        if ($medicalDocs->isNotEmpty()) {
            $formattedText .= "مدارک پزشکی:\n";
            foreach ($medicalDocs as $doc) {
                $formattedText .= "- {$doc->name}\n";
                $formattedText .= "  توضیحات: {$doc->description}\n";
                $files = $doc->files;
                foreach ($files as $file) {
                    $formattedText .= "  تصویر: " . asset('storage/' . $file['path']) . "\n";
                }
            }
            $formattedText .= "\n";
        }

        // Add drug documents information
        if ($drugDocs->isNotEmpty()) {
            $formattedText .= "داروها:\n";
            foreach ($drugDocs as $doc) {
                $formattedText .= "- {$doc->name}\n";
                $formattedText .= "  توضیحات: {$doc->description}\n";
                $files = $doc->files;
                foreach ($files as $file) {
                    $formattedText .= "  تصویر: " . asset('storage/' . $file['path']) . "\n";
                }
            }
            $formattedText .= "\n";
        }
        $formattedText .= "Please write me a long professional report with information (about patient introduction and personal information), risk factors, lifestyle status, how many percent of the symptoms are typical and how many percent are atypical, write in percentage and separate analysis, what is the pretest probability of CAD in this patient. Write about differential diagnoses. Write the patient's economic status and say if he is willing to do diagnostic tests. Write a report of the analysis of medical documents and images, for example, history, echocardiography, CT angiography, angiography and other documents along with the date of the report. Write this text in English";

        // Call OpenAI API
        $client = (new Factory)
            ->withApiKey(config('services.openai.api_key'))
            ->withHttpClient(new \GuzzleHttp\Client(['verify' => false]))
            ->make();

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a medical assistant analyzing patient assessment data and medical documents.'
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $formattedText
                        ],
                        // Add image URLs for medical docs
                        ...$medicalDocs->flatMap(function($doc) {
                            $files = $doc->files;
                            return collect($files)->map(function($file) {
                                $path = storage_path('app/public/' . $file['path']);
                                $imageData = base64_encode(file_get_contents($path));
                                return [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:{$file['mime_type']};base64,{$imageData}",
                                        'detail' => 'low'
                                    ]
                                ];
                            });
                        })->toArray(),
                        // Add image URLs for drug docs
                        ...$drugDocs->flatMap(function($doc) {
                            $files = $doc->files;
                            return collect($files)->map(function($file) {
                                $path = storage_path('app/public/' . $file['path']);
                                $imageData = base64_encode(file_get_contents($path));
                                return [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:{$file['mime_type']};base64,{$imageData}",
                                        'detail' => 'low'
                                    ]
                                ];
                            });
                        })->toArray()
                    ]
                ]
            ],
            'max_completion_tokens' => 4000
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'prompt' => $formattedText,
                'response' => $response->choices[0]->message->content
            ]
        ]);
    }

    /**
     * Upload documents for assessment
     */
    public function uploadDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required|exists:user_assessments,id',
            'document_type' => 'required|in:drugs,medical_docs',
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $assessment = UserAssessment::findOrFail($request->assessment_id);

        // Check if this assessment belongs to the authenticated user
        if ($assessment->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Save file
        $file = $request->file('file');
        $path = $file->store('assessment_documents', 'public');

        $document = UserAssessmentDocument::create([
            'user_assessment_id' => $assessment->id,
            'document_type' => $request->document_type,
            'name' => $request->name,
            'file_path' => $path,
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'document' => $document
        ]);
    }

    /**
     * Complete assessment
     */
    public function complete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required|exists:user_assessments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $assessment = UserAssessment::findOrFail($request->assessment_id);

        // Check if this assessment belongs to the authenticated user
        if ($assessment->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $assessment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Assessment completed successfully',
            'assessment' => $assessment
        ]);
    }

    /**
     * Get assessment by ID
     */
    public function show(Request $request, $id)
    {
        $assessment = UserAssessment::findOrFail($id);

        // Check if this assessment belongs to the authenticated user
        if ($assessment->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'assessment' => $assessment,
            'answers' => $assessment->answers,
            'documents' => $assessment->documents
        ]);
    }

    /**
     * Get all assessments for the authenticated user
     */
    public function index(Request $request)
    {
        $assessments = $request->user()->assessments()->latest()->get();

        return response()->json([
            'assessments' => $assessments
        ]);
    }

    /**
     * Get questions for a specific section
     */
    public function getQuestions(Request $request, $section)
    {
        $validSections = ['form', 'drugs', 'medical_docs', 'additional_info'];

        if (!in_array($section, $validSections)) {
            return response()->json(['error' => 'Invalid section'], 400);
        }

        $questions = AssessmentQuestion::where('section', $section)
            ->where('active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'questions' => $questions
        ]);
    }

    /**
     * Get all assessment series with their questions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSeriesWithQuestions()
    {
        try {
            // Get all series with their questions using eager loading
            $seriesWithQuestions = AssessmentSeries::with(['questions' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

            return response()->json([
                'status' => 'success',
                'data' => $seriesWithQuestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch assessment data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the current user's assessment with answers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentAssessment()
    {
        try {
            $user = User::find(Auth::id());

            // Find or create an assessment for this user
            $assessment = UserAssessment::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'in_progress']
            );

            // Get all answers grouped by series_id
            $answers = $assessment->answers()
                ->select('series_id', 'question_id')
                ->get()
                ->groupBy('series_id')
                ->map(function($group) {
                    return $group->pluck('question_id')->toArray();
                })
                ->toArray();

            // Get all notes grouped by series_id
            $notes = $assessment->notes()
                ->select('series_id', 'notes')
                ->get()
                ->keyBy('series_id')
                ->map(function($item) {
                    return $item->notes;
                })
                ->toArray();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'assessment_id' => $assessment->id,
                    'status' => $assessment->status,
                    'completed_at' => $assessment->completed_at,
                    'answers' => $answers,
                    'notes' => $notes
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch current assessment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

