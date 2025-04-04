@php
    $files = $getRecord()->files ?? [];
@endphp

@pushOnce('styles')
    <link href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css" rel="stylesheet">
    <style>
        .drug-docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1rem 0;
        }
        .drug-doc-item {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }
        .drug-doc-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .drug-doc-item:hover img {
            transform: scale(1.05);
        }
    </style>
@endPushOnce

@if (count($files) > 0)
    <div class="drug-docs-grid">
        @foreach ($files as $file)
            <a href="{{ Storage::url($file['path']) }}"
               class="drug-doc-item glightbox"
               data-gallery="drug-docs">
                <img src="{{ Storage::url($file['path']) }}"
                     alt="Drug document"
                     loading="lazy">
            </a>
        @endforeach
    </div>
@else
    <div class="text-gray-500 text-center py-4">
        {{ __('No files uploaded') }}
    </div>
@endif

@pushOnce('scripts')
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            GLightbox({
                touchNavigation: true,
                loop: true,
                autoplayVideos: true
            });
        });
    </script>
@endPushOnce
