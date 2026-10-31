@props([
    'media' => null,
    'src' => null,
    'mime' => null,
    'name' => null,
    'height' => '600px',
])

@php
    // Accepts a spatie/laravel-medialibrary Media model, or a plain URL with an optional MIME type.
    if ($media instanceof \Spatie\MediaLibrary\MediaCollections\Models\Media) {
        $src ??= $media->getUrl();
        $mime ??= $media->mime_type;
        $name ??= $media->file_name;
    }

    $name ??= basename((string) parse_url((string) $src, PHP_URL_PATH));
    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $mime = strtolower((string) $mime);

    $kind = match (true) {
        str_starts_with($mime, 'image/') || in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg'], true) => 'image',
        $mime === 'application/pdf' || $extension === 'pdf' => 'pdf',
        str_starts_with($mime, 'video/') || in_array($extension, ['mp4', 'webm', 'ogv', 'mov'], true) => 'video',
        str_starts_with($mime, 'audio/') || in_array($extension, ['mp3', 'wav', 'ogg', 'oga', 'm4a', 'aac', 'flac'], true) => 'audio',
        default => 'file',
    };
@endphp

<figure {{ $attributes->merge(['class' => 'w-full overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-neutral-700 dark:bg-neutral-800']) }} data-media-preview="{{ $kind }}">
    @switch($kind)
        @case('image')
            <img src="{{ $src }}" alt="{{ $name }}" loading="lazy" class="mx-auto block w-auto max-w-full object-contain" style="max-height: {{ $height }}">
            @break

        @case('pdf')
            <object data="{{ $src }}" type="application/pdf" class="block w-full" style="height: {{ $height }}">
                <p class="p-4 text-sm text-gray-600 dark:text-neutral-400">
                    {{ __('This browser cannot show PDFs inline.') }}
                    <a href="{{ $src }}" target="_blank" rel="noopener" class="font-medium text-blue-600 underline dark:text-blue-400">{{ __('Open :name', ['name' => $name]) }}</a>
                </p>
            </object>
            @break

        @case('video')
            <video src="{{ $src }}" controls preload="metadata" class="block w-full bg-black" style="max-height: {{ $height }}">
                <a href="{{ $src }}">{{ $name }}</a>
            </video>
            @break

        @case('audio')
            <div class="p-4">
                <audio src="{{ $src }}" controls preload="metadata" class="w-full">
                    <a href="{{ $src }}">{{ $name }}</a>
                </audio>
            </div>
            @break

        @default
            {{-- Office files and others have no native browser viewer: offer the file instead. --}}
            <div class="flex items-center justify-between gap-4 p-4">
                <span class="truncate text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $name }}</span>
                <a href="{{ $src }}" target="_blank" rel="noopener" download class="inline-flex shrink-0 items-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-800 dark:border-neutral-700 dark:text-neutral-200">
                    {{ __('Download') }}
                </a>
            </div>
    @endswitch

    @if($kind !== 'file')
        <figcaption class="truncate border-t border-gray-200 px-4 py-2 text-xs text-gray-500 dark:border-neutral-700 dark:text-neutral-400">{{ $name }}</figcaption>
    @endif
</figure>
