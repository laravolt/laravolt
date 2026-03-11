@php
    $avatars = $avatars ?? [];
    $max = $max ?? null;
    $size = $size ?? 'md';
    $sizeClasses = [
        'xs' => 'size-6 text-[8px]',
        'sm' => 'size-8 text-xs',
        'md' => 'size-10 text-sm',
        'lg' => 'size-12 text-base',
    ];
    $currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $displayAvatars = $max ? array_slice($avatars, 0, $max) : $avatars;
    $remaining = $max && count($avatars) > $max ? count($avatars) - $max : 0;
@endphp

<div class="flex -space-x-2" {{ $attributes->except(['avatars', 'max', 'size']) }}>
    @foreach($displayAvatars as $avatar)
        @php
            $img = is_array($avatar) ? ($avatar['image'] ?? null) : null;
            $name = is_array($avatar) ? ($avatar['name'] ?? '?') : $avatar;
            $initials = collect(explode(' ', $name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
            $bgColors = ['bg-blue-600','bg-teal-600','bg-red-600','bg-yellow-500','bg-purple-600','bg-pink-600','bg-indigo-600','bg-green-600'];
            $bgColor = $bgColors[crc32($name) % count($bgColors)];
        @endphp
        @if($img)
            <img class="inline-block {{ $currentSize }} rounded-full ring-2 ring-white dark:ring-neutral-900" src="{{ $img }}" alt="{{ $name }}" title="{{ $name }}">
        @else
            <span class="inline-flex items-center justify-center {{ $currentSize }} rounded-full {{ $bgColor }} ring-2 ring-white dark:ring-neutral-900 text-white font-medium" title="{{ $name }}">
                {{ $initials }}
            </span>
        @endif
    @endforeach

    @if($remaining > 0)
        <span class="inline-flex items-center justify-center {{ $currentSize }} rounded-full bg-gray-200 ring-2 ring-white dark:bg-neutral-700 dark:ring-neutral-900 text-gray-700 dark:text-neutral-300 font-medium">
            +{{ $remaining }}
        </span>
    @endif
</div>
