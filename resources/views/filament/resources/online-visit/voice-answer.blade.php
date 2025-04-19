@php
    $record = $getRecord();
    $voiceAnswer = $record->voice_answer;
    $duration = $record->voice_answer_duration;

    // Check if voice_answer exists and is not empty
    if (!empty($voiceAnswer)) {
        $audioUrl = Storage::disk('public')->url($voiceAnswer);
    }
@endphp

@if(!empty($voiceAnswer))
<div class="space-y-2">
    <div class="flex items-center space-x-2 rtl:space-x-reverse">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
            </svg>
        </div>
        <div class="flex-grow">
            <div class="text-sm font-medium text-gray-900 dark:text-white">پاسخ صوتی</div>
            @if($duration)
                <div class="text-xs text-gray-500 dark:text-gray-400">مدت زمان: {{ floor($duration / 60) }}:{{ str_pad($duration % 60, 2, '0', STR_PAD_LEFT) }}</div>
            @endif
        </div>
    </div>

    <div class="mt-2">
        <audio controls class="w-full">
            <source src="{{ $audioUrl }}" type="audio/mpeg">
            مرورگر شما از پخش فایل صوتی پشتیبانی نمی‌کند.
        </audio>
    </div>

    <div class="mt-2">
        <a href="{{ $audioUrl }}" download class="inline-flex items-center px-3 py-1 text-sm font-medium text-primary-600 bg-primary-50 rounded-md hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <svg class="w-4 h-4 ml-1 rtl:ml-reverse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            دانلود فایل صوتی
        </a>
    </div>
</div>
@else
<div class="text-gray-500 text-center py-4">
    پاسخ صوتی موجود نیست
</div>
@endif
