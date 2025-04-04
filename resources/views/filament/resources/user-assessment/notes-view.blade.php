@php
    $series = \App\Models\AssessmentSeries::orderBy('order')->get();
    $notes = $getRecord()->notes()->get()->keyBy('series_id');
@endphp

<div class="space-y-4">
    @if($notes->count() > 0)
        @foreach($notes as $seriesId => $note)
            @php
                $seriesTitle = $series->firstWhere('series_id', $seriesId)->title ?? "سری {$seriesId}";
            @endphp
            <div class="rounded-lg border border-gray-300 p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $seriesTitle }}</h3>
                <p class="text-sm text-black whitespace-pre-wrap">{{ $note->notes }}</p>
            </div>
        @endforeach
    @else
        <div class="text-center py-4 text-gray-500">
            یادداشتی ثبت نشده است
        </div>
    @endif
</div>