@foreach($items->sortBy(fn($item) => $item->data('order')) as $item)
    @if($item->hasChildren())
        <div class="text-[13px] mt-2 text-gray-500 dark:text-neutral-400">
            <div class="flex items-center justify-between px-3 py-2 cursor-pointer">
                <div class="flex items-center gap-x-2">
                    {!! svg(config('laravolt.ui.iconset').'-'.$item->data('icon'), null, ['class' => 'size-4'])
                    ->toHtml() !!}
                    <span>{{ $item->title }}</span>
                </div>
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="ms-3 space-y-1">
                @foreach($item->children()->sortBy(fn($item) => $item->data('order')) as $child)
                    @if($child->hasChildren())
                        <div class="px-3 py-1 text-gray-500">{{ $child->title }}</div>
                        <div class="ms-3 space-y-1">
                            @foreach($child->children() as $grandchild)
                                <a class="block px-3 py-1 rounded hover:bg-gray-100 dark:hover:bg-neutral-700 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($grandchild->children(), $grandchild->isActive) }}" href="{{ $grandchild->url() }}" data-parent="{{ $grandchild->parent()->title }}">
                                    <span>{{ $grandchild->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <a href="{{ $child->url() }}" data-parent="{{ $child->parent()->title }}" class="block px-3 py-1 rounded hover:bg-gray-100 dark:hover:bg-neutral-700 {{ ($child->isActive)?'bg-gray-100 dark:bg-neutral-700':'' }}">{{ $child->title }}</a>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <a class="flex items-center gap-x-2 px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-neutral-700 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) }}" href="{{ $item->url() }}" data-parent="{{ $item->parent()->title }}">
            {!! svg(config('laravolt.ui.iconset').'-'.$item->data('icon'), null, ['class' => 'size-4'])
            ->toHtml() !!}
            <span>{{ $item->title }}</span>
        </a>
    @endif
@endforeach
