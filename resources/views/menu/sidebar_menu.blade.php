<div class="sidebar__menu">
    @if(!$items->isEmpty())
        @if(config('laravolt.platform.features.quick_switcher'))
            @include('laravolt::quick-switcher.modal')
        @endif

        <div class="space-y-1" data-role="original-menu">

            @foreach($items as $item)
                @if($item->hasChildren())
                    <div class="px-3 pt-3 text-xs font-semibold uppercase text-gray-500">{{ $item->title }}</div>
                    <div class="hs-accordion-group" data-role="sidenav">
                        @include('laravolt::menu.sidebar_items', ['items' => $item->children()])
                    </div>
                @else
                    <div class="hs-accordion-group">
                        <a class="flex items-center gap-x-2 px-3 py-2 text-sm rounded-md {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}"
                           href="{{ $item->url() }}">
                            {!! svg(config('laravolt.ui.iconset').'-'.$item->data('icon'), null, ['class' => 'h-4 w-4'])
                            ->width('16px')
                            ->toHtml() !!}
                            <span>{{ $item->title }}</span>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
