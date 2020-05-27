@push('tab.titles.'.\Laravolt\Platform\Components\TabComponent::getActiveTab())
    <a {{ $attributes->merge(['class' => "item $activeClass"]) }} data-tab="{{ $key }}">{!! $title !!}</a>
@endpush

@push('tab.contents.'.\Laravolt\Platform\Components\TabComponent::getActiveTab())
    <div {{ $attributes->merge(['class' => "ui bottom attached tab basic segment $activeClass"]) }} data-tab="{{ $key }}">
        {!! $slot !!}
    </div>
@endpush
