<div class="sidebar__menu">
    @if(!$items->isEmpty())
        @if(config('laravolt.platform.features.quick_switcher'))
            @include('laravolt::quick-switcher.modal')
        @endif

        <nav class="space-y-1" data-role="original-menu">

            @foreach($items as $item)
                @if($item->hasChildren())
                    <div class="px-2 text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-400">{{ $item->title }}</div>
                    <div class="space-y-1" data-role="sidenav">
                        @include('laravolt::menu.sidebar_items', ['items' => $item->children()])
                    </div>
                @else
                    <div>
                        <a class="flex items-center gap-x-2 px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-neutral-700 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) }}" href="{{ $item->url() }}">
                            <i class="{{ $item->data('icon') }}"></i>
                            <span>{{ $item->title }}</span>
                        </a>
                    </div>
                @endif
            @endforeach
        </nav>
    @endif
</div>
