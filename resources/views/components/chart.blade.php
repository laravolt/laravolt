@php
    $id = $attributes->get('id', 'chart-' . uniqid());
    $type = $attributes->get('type', 'bar');
    $series = $attributes->get('series', []);
    $categories = $attributes->get('categories', []);
    $height = $attributes->get('height', 320);
    $title = $attributes->get('title', null);
    $colors = $attributes->get('colors', ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']);
    $stacked = $attributes->get('stacked', false);
    $toolbar = $attributes->get('toolbar', true);
    $chartConfig = [
        'chart' => [
            'type' => $type,
            'height' => $height,
            'toolbar' => ['show' => $toolbar],
            'stacked' => $stacked,
            'fontFamily' => 'Inter, ui-sans-serif, system-ui, sans-serif',
        ],
        'series' => $series,
        'colors' => $colors,
        'plotOptions' => [
            'bar' => ['borderRadius' => 4, 'columnWidth' => '50%'],
        ],
        'dataLabels' => ['enabled' => false],
        'stroke' => ['curve' => 'smooth', 'width' => in_array($type, ['line', 'area']) ? 2 : 0],
        'fill' => ['opacity' => $type === 'area' ? 0.3 : 1],
        'grid' => [
            'borderColor' => '#e5e7eb',
            'strokeDashArray' => 4,
            'padding' => ['left' => 2, 'right' => 2],
        ],
        'legend' => ['position' => 'top', 'horizontalAlign' => 'right'],
        'tooltip' => ['theme' => 'dark'],
    ];

    if (!empty($categories) && !in_array($type, ['pie', 'donut', 'radialBar'])) {
        $chartConfig['xaxis'] = [
            'categories' => $categories,
            'labels' => ['style' => ['colors' => '#9ca3af', 'fontSize' => '12px']],
        ];
        $chartConfig['yaxis'] = [
            'labels' => ['style' => ['colors' => '#9ca3af', 'fontSize' => '12px']],
        ];
    }

    if (in_array($type, ['pie', 'donut'])) {
        $chartConfig['labels'] = $categories;
        $chartConfig['responsive'] = [['breakpoint' => 480, 'options' => ['chart' => ['width' => 280], 'legend' => ['position' => 'bottom']]]];
    }

    if ($title) {
        $chartConfig['title'] = ['text' => $title, 'style' => ['fontSize' => '14px', 'fontWeight' => 600, 'color' => '#374151']];
    }
@endphp

<div id="{{ $id }}" {{ $attributes->except(['type', 'series', 'categories', 'height', 'title', 'colors', 'stacked', 'toolbar']) }}></div>

@pushOnce('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endPushOnce

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    new ApexCharts(document.getElementById('{{ $id }}'), {!! json_encode($chartConfig) !!}).render();
});
</script>
@endpush
