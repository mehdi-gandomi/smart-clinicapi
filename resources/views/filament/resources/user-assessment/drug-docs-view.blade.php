@php
    $drugDocs = $getRecord()->drugsDocuments()->get();
@endphp

<!-- Include GLightbox CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<style>
    /* RTL fixes for lightbox */
    .gslide-description {
        text-align: right;
        direction: rtl;
    }
    .gclose {
        right: auto;
        left: 10px;
    }
</style>

<div class="space-y-4">
    @if($drugDocs->count() > 0)
        @foreach($drugDocs as $doc)
            <div class="rounded-lg border border-gray-300 p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $doc->name }}</h3>
                <p class="text-sm text-gray-600 mb-3">{{ $doc->description }}</p>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($doc->files as $index => $file)
                        <a
                            href="{{ asset('storage/' . $file['path']) }}"
                            class="glightbox relative group"
                            data-gallery="drug-gallery-{{$doc->id}}"
                            data-glightbox="title: {{ $doc->name }}; description: {{ $doc->description }}"
                        >
                            <img
                                src="{{ asset('storage/' . $file['path']) }}"
                                alt="{{ $doc->name }}"
                                class="w-full h-32 object-cover rounded-lg cursor-pointer"
                            />
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-opacity rounded-lg flex items-center justify-center">
                                <span class="bg-white bg-opacity-75 hover:bg-opacity-100 text-gray-800 font-semibold py-1 px-2 rounded shadow text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                    بزرگنمایی
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-4 text-gray-500">
            دارویی ثبت نشده است
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
    // Initialize GLightbox
    document.addEventListener('DOMContentLoaded', function() {
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: false,
            openEffect: 'zoom',
            closeEffect: 'fade',
            cssEfects: {
                fade: { in: 'fadeIn', out: 'fadeOut' },
                zoom: { in: 'zoomIn', out: 'zoomOut' }
            },
            draggable: false,
        });
    });
</script>
