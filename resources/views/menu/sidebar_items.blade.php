@foreach($items->sortBy(fn($item) => $item->data('order')) as $item)
    @if($item->hasChildren())
        <div class="hs-accordion" id="sidenav-item-{{ Str::slug($item->title) }}">
            <button class="hs-accordion-toggle w-full flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) ? 'font-semibold text-teal-700' : '' }}">
                <span class="inline-flex items-center gap-x-2">
                    {!! svg(config('laravolt.ui.iconset').'-'.$item->data('icon'), null, ['class' => 'h-4 w-4'])
                    ->width('16px')
                    ->toHtml() !!}
                    {{ $item->title }}
                </span>
                <svg class="hs-accordion-active:block hidden h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                <svg class="hs-accordion-active:hidden block h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="hs-accordion-content hidden pl-4">
                <div class="space-y-1">
                    @foreach($item->children()->sortBy(fn($item) => $item->data('order')) as $child)
                        @if($child->hasChildren())
                            <div class="hs-accordion" id="sidenav-sub-{{ Str::slug($child->title) }}">
                                <button class="hs-accordion-toggle w-full flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($child->children(), $child->isActive) ? 'font-semibold text-teal-700' : '' }}">
                                    <span>{{ $child->title }}</span>
                                    <svg class="hs-accordion-active:block hidden h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    <svg class="hs-accordion-active:hidden block h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="hs-accordion-content hidden pl-3">
                                    <div class="space-y-1">
                                        @foreach($child->children() as $grandchild)
                                            <a class="block px-3 py-1.5 text-sm rounded-md {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($grandchild->children(), $grandchild->isActive) ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}"
                                               href="{{ $grandchild->url() }}"
                                               data-parent="{{ $grandchild->parent()->title }}">
                                                <span>{{ $grandchild->title }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ $child->url() }}" data-parent="{{ $child->parent()->title }}"
                               class="block px-3 py-1.5 text-sm rounded-md {{ ($child->isActive)?'bg-teal-50 text-teal-700':'text-gray-700 hover:bg-gray-50' }} ">{{ $child->title }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <a class="block px-3 py-2 text-sm rounded-md {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}"
           href="{{ $item->url() }}"
           data-parent="{{ $item->parent()->title }}">
            {!! svg(config('laravolt.ui.iconset').'-'.$item->data('icon'), null, ['class' => 'h-4 w-4'])
            ->width('16px')
            ->toHtml() !!}
            <span>{{ $item->title }}</span>
        </a>
        <div></div>
    @endif
@endforeach
