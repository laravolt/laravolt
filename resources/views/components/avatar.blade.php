@php
    $size = $attributes->get('size', 'md');
    $src = $attributes->get('src', null);
    $alt = $attributes->get('alt', '');
    $initials = $attributes->get('initials', '');
    $status = $attributes->get('status', null);
    $badge = $attributes->get('badge', null);
    $attributes = $attributes->except(['size', 'src', 'alt', 'initials', 'status', 'badge']);

    // Size variants
    $sizeClasses = [
        'xs' => 'w-6 h-6 text-xs',
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-base',
        'lg' => 'w-12 h-12 text-lg',
        'xl' => 'w-16 h-16 text-xl',
        '2xl' => 'w-20 h-20 text-2xl'
    ];

    // Status indicator colors
    $statusColors = [
        'online' => 'bg-green-500',
        'offline' => 'bg-gray-400',
        'away' => 'bg-yellow-500',
        'busy' => 'bg-red-500'
    ];

    $baseClasses = 'inline-flex items-center justify-center rounded-full bg-gray-100 text-gray-800 font-medium overflow-hidden dark:bg-neutral-700 dark:text-neutral-200';
    $classes = $baseClasses . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

<div {{ $attributes->merge(['class' => 'relative inline-block']) }}>
    @if($src)
        <!-- Avatar with image -->
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="{{ $classes }} object-cover"
        />
    @else
        <!-- Avatar with initials -->
        <span class="{{ $classes }}">
            {{ $initials ?: substr($alt, 0, 2) }}
        </span>
    @endif

    <!-- Status indicator -->
    @if($status)
        @php
            $statusSize = match($size) {
                'xs' => 'w-1.5 h-1.5',
                'sm' => 'w-2 h-2',
                'md' => 'w-2.5 h-2.5',
                'lg' => 'w-3 h-3',
                'xl' => 'w-4 h-4',
                '2xl' => 'w-5 h-5',
                default => 'w-2.5 h-2.5'
            };
        @endphp
        <span class="absolute -bottom-0.5 -right-0.5 {{ $statusSize }} {{ $statusColors[$status] ?? $statusColors['online'] }} border-2 border-white rounded-full dark:border-neutral-800"></span>
    @endif

    <!-- Badge -->
    @if($badge)
        @php
            $badgeSize = match($size) {
                'xs', 'sm' => 'w-4 h-4 text-xs',
                'md' => 'w-5 h-5 text-xs',
                'lg' => 'w-6 h-6 text-sm',
                'xl' => 'w-8 h-8 text-sm',
                '2xl' => 'w-10 h-10 text-base',
                default => 'w-5 h-5 text-xs'
            };
        @endphp
        <span class="absolute -top-1 -right-1 {{ $badgeSize }} bg-red-500 text-white rounded-full flex items-center justify-center font-bold border-2 border-white dark:border-neutral-800">
            {{ $badge }}
        </span>
    @endif
</div>
