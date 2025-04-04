<div class="p-4">
    @if($error)
        <div class="text-red-600 dark:text-red-400">
            {{ $error }}
        </div>
    @else
        <div class="prose dark:prose-invert max-w-none">
            {!! Str::markdown($response) !!}
        </div>
    @endif
</div> 