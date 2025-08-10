<div>
    @if(!$items->isEmpty())
        @if(config('laravolt.platform.features.quick_switcher'))
            @include('laravolt::quick-switcher.modal')
        @endif

        <ul class="flex flex-col gap-y-1" data-role="original-menu">
            @foreach($items as $item)
                @if($item->hasChildren())
                    <li class="px-2 lg:px-3 pt-3 text-[10px] font-semibold uppercase text-gray-500">{{ $item->title }}</li>
                    <li class="px-1 lg:px-2">
                        <div class="hs-accordion-group" data-role="sidenav">
                            @include('laravolt::menu.sidebar_items', ['items' => $item->children()])
                        </div>
                    </li>
                @else
                    <li class="px-1 lg:px-2">
                        <div class="hs-accordion-group">
                            <a class="flex items-center gap-x-2 px-3 py-2 text-sm rounded-lg {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-neutral-200' : 'text-gray-700 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700' }}"
                               href="{{ $item->url() }}">
                                {!! svg(config('laravolt.ui.iconset').'-'.$item->data('icon'), null, ['class' => 'h-4 w-4'])
                                ->width('16px')
                                ->toHtml() !!}
                                <span>{{ $item->title }}</span>
                            </a>
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</div>
