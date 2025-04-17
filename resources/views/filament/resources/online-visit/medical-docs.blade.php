@php
    $medicalDocs = $medicalDocs ?? collect([]);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach ($medicalDocs as $doc)
        @php
            $isImage = Str::startsWith($doc['mime_type'] ?? '', 'image/');
            $url = Storage::disk('public')->url($doc['path'] ?? '');
        @endphp

        <div class="relative group">
            @if ($isImage)
                <a href="{{ $url }}" class="glightbox" data-gallery="gallery">
                    <img src="{{ $url }}" alt="{{ $doc['original_name'] ?? '' }}"
                         class="w-full h-32 object-cover rounded-lg shadow-md transition-transform duration-300 group-hover:scale-105">
                </a>
            @else
                <a href="{{ $url }}" target="_blank" class="block p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">{{ $doc['original_name'] ?? '' }}</p>
                            <p class="text-sm text-gray-500">{{ number_format(($doc['size'] ?? 0) / 1024, 2) }} KB</p>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    @endforeach

    @if ($medicalDocs->isEmpty())
        <div class="col-span-full text-center py-4 text-gray-500">
            هیچ مدرکی آپلود نشده است.
        </div>
    @endif
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
<script>
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });
</script>
