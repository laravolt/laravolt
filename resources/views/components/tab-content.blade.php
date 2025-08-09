@push('tab.titles.'.\Laravolt\Platform\Components\Tab::getActiveTab())
    <button type="button" {{ $attributes->merge(['class' => "hs-tab-active:font-semibold hs-tab-active:border-teal-600 hs-tab-active:text-teal-600 -mb-px py-2 px-3 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm text-gray-500 hover:text-gray-700 $activeClass"]) }} data-hs-tab="#tab-{{ $key }}" aria-controls="tab-{{ $key }}">{!! $title !!}</button>
@endpush

@push('tab.contents.'.\Laravolt\Platform\Components\Tab::getActiveTab())
    <div id="tab-{{ $key }}" role="tabpanel" {{ $attributes->merge(['class' => "hidden $activeClass"]) }}>
        {!! $slot !!}
    </div>
@endpush
