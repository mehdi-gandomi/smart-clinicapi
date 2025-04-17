@php
    $voiceNote = $voiceNote ?? null;
@endphp

@if ($voiceNote)
    <div class="w-full">
        <audio controls class="w-full">
            <source src="{{ Storage::disk('public')->url($voiceNote) }}" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    </div>
@else
    <div class="text-center py-4 text-gray-500">
        هیچ یادداشت صوتی وجود ندارد.
    </div>
@endif
