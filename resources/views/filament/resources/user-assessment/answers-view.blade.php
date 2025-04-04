@php
    $assessment = $getRecord();
    $series = \App\Models\AssessmentSeries::with(['questions' => function ($query) {
        $query->orderBy('order');
    }])->orderBy('order')->get();

    $answers = $assessment->answers()
        ->with('question')
        ->get()
        ->groupBy('series_id');

    $notes = $assessment->notes()
        ->get()
        ->keyBy('series_id');
@endphp

<div class="space-y-6">
    @foreach($series as $s)
        @php
            $seriesAnswers = $answers->get($s->series_id, collect());
            $seriesNote = $notes->get($s->series_id);
        @endphp
        @if($seriesAnswers->isNotEmpty())
            <div class="rounded-lg border border-gray-300 shadow-sm hover:shadow-md transition-shadow duration-200 p-5">
                <h3 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2 px-3">{{ $s->title }}</h3>
                <div class="space-y-2 p-3">
                    @foreach($s->questions as $question)
                        @php
                            $answer = $seriesAnswers->first(function($a) use ($question) {
                                return $a->question_id === $question->question_id;
                            });
                        @endphp
                        @if($answer)
                            <div class="flex flex-col space-y-2 border-r-4 border-indigo-500 pr-3 py-1">
                                <span class="text-sm font-semibold text-black">{{ $question->text }}</span>
                                @if($answer->answer)
                                    <span class="text-lg font-medium text-indigo-700 bg-indigo-50 p-2 rounded">{{ $answer->answer }}</span>
                                @endif
                            </div>
                        @endif
                    @endforeach

                    @if($seriesNote)
                        <div class="mt-4 border-t pt-3">
                            <div class="bg-amber-50 border-l-4 border-amber-500 p-3 rounded">
                                <h4 class="text-sm font-semibold text-amber-800 mb-1">یادداشت بیمار:</h4>
                                <p class="text-amber-700 whitespace-pre-wrap">{{ $seriesNote->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endforeach
</div>