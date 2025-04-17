@php
    $record = $getRecord();
    $voiceAnswer = $record->voice_answer;
    $duration = $record->voice_answer_duration;
@endphp

@if($voiceAnswer)
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">مدت زمان: {{ $duration ?: '--:--' }}</span>
        </div>
        <div class="w-full">
            <audio controls class="w-full">
                <source src="{{ Storage::url($voiceAnswer) }}" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
        </div>
    </div>
@else
    <div class="text-gray-500 text-center py-4">
        پاسخ صوتی موجود نیست
    </div>
@endif
