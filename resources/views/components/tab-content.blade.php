@push('tab.titles.'.\Laravolt\Platform\Components\Tab::getActiveTab())
    <a {{ $attributes->merge(['class' => "item $activeClass"]) }} data-tab="{{ $key }}">{!! $title !!}</a>
@endpush

@push('tab.contents.'.\Laravolt\Platform\Components\Tab::getActiveTab())
    <div {{ $attributes->merge(['class' => "ui bottom attached tab segment $activeClass"]) }} data-tab="{{ $key }}">
        {!! $slot !!}
    </div>
@endpush
