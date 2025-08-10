@foreach($items->sortBy(fn($item) => $item->data('order')) as $item)
    @if($item->hasChildren())
        <div class="hs-accordion" id="sidenav-item-{{ Str::slug($item->title) }}">
            <button class="hs-accordion-toggle w-full text-start flex items-center justify-between px-3 py-2 text-sm rounded-lg text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:hover:bg-neutral-700 dark:text-neutral-300 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) ? 'bg-gray-100 dark:bg-neutral-700 font-medium' : '' }}">
                <span class="inline-flex items-center gap-x-2">
                    {!! svg(config('laravolt.ui.iconset').'-'.$item->data('icon'), null, ['class' => 'h-4 w-4'])
                    ->width('16px')
                    ->toHtml() !!}
                    {{ $item->title }}
                </span>
                <svg class="hs-accordion-active:-rotate-180 shrink-0 mt-1 h-3.5 w-3.5 transition" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="sidenav-item-{{ Str::slug($item->title) }}" style="display: none;">
                <ul class="hs-accordion-group ps-7 mt-1 flex flex-col gap-y-1 relative before:absolute before:top-0 before:start-4.5 before:w-0.5 before:h-full before:bg-gray-100 dark:before:bg-neutral-700" data-hs-accordion-always-open>
                    @foreach($item->children()->sortBy(fn($item) => $item->data('order')) as $child)
                        @if($child->hasChildren())
                            <li class="hs-accordion" id="sidenav-sub-{{ Str::slug($child->title) }}">
                                <button class="hs-accordion-toggle w-full text-start flex items-center justify-between px-3 py-2 text-sm rounded-lg text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:hover:bg-neutral-700 dark:text-neutral-300 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($child->children(), $child->isActive) ? 'bg-gray-100 dark:bg-neutral-700 font-medium' : '' }}">
                                    <span>{{ $child->title }}</span>
                                    <svg class="hs-accordion-active:-rotate-180 shrink-0 mt-1 h-3.5 w-3.5 transition" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <div class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="sidenav-sub-{{ Str::slug($child->title) }}" style="display: none;">
                                    <ul class="ps-6 mt-1 flex flex-col gap-y-1">
                                        @foreach($child->children() as $grandchild)
                                            <li>
                                                <a class="flex gap-x-4 py-2 px-3 text-sm rounded-lg {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($grandchild->children(), $grandchild->isActive) ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-neutral-200' : 'text-gray-700 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700' }}"
                                                   href="{{ $grandchild->url() }}"
                                                   data-parent="{{ $grandchild->parent()->title }}">
                                                    <span>{{ $grandchild->title }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @else
                            <li>
                                <a href="{{ $child->url() }}" data-parent="{{ $child->parent()->title }}"
                                   class="flex gap-x-4 py-2 px-3 text-sm rounded-lg {{ ($child->isActive)?'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-neutral-200':'text-gray-700 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700' }} ">{{ $child->title }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <a class="flex items-center gap-x-2 px-3 py-2 text-sm rounded-lg {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-neutral-200' : 'text-gray-700 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700' }}"
           href="{{ $item->url() }}"
           data-parent="{{ $item->parent()->title }}">
            {!! svg(config('laravolt.ui.iconset').'-'.$item->data('icon'), null, ['class' => 'h-4 w-4'])
            ->width('16px')
            ->toHtml() !!}
            <span>{{ $item->title }}</span>
        </a>
    @endif
@endforeach
