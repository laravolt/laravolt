@push('tab.titles.'.\Laravolt\Platform\Components\Tab::getActiveTab())
    <a {{ $attributes->merge(['class' => "px-3 py-2 -mb-px border-b-2 $activeClass"]) }} data-tab="{{ $key }}">{!! $title !!}</a>
@endpush

@push('tab.contents.'.\Laravolt\Platform\Components\Tab::getActiveTab())
    <div {{ $attributes->merge(['class' => "py-4 $activeClass"]) }} data-tab="{{ $key }}">
        {!! $slot !!}
    </div>
@endpush
