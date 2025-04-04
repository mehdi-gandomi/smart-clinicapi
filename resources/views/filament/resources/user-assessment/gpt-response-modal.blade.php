<div class="space-y-4">
    @if($error)
        <div class="p-4 bg-danger-50 text-danger-700 rounded-lg">
            {{ $error }}
        </div>
    @else
        <div class="prose max-w-none dark:prose-invert">
            {!! \Illuminate\Support\Str::markdown($response) !!}
        </div>
    @endif
</div> 