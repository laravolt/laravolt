<div class="sidebar__menu">
    @if(!$items->isEmpty())
        @if(config('laravolt.platform.features.quick_switcher'))
            @include('laravolt::quick-switcher.modal')
        @endif

        <div class="ui attached vertical menu fluid" data-role="original-menu">

            @foreach($items as $item)
                @if($item->hasChildren())
                    <div class="item">
                        <div class="header">{{ $item->title }}</div>
                    </div>
                    <div class="ui accordion sidebar__accordion" data-role="sidenav">
                        @include('laravolt::menu.sidebar_items', ['items' => $item->children()])
                    </div>
                @else
                    <div class="ui accordion sidebar__accordion">
                        <a class="title title__1 item empty {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) }}"
                           href="{{ $item->url() }}">
                            <i class="left icon {{ $item->data('icon') }}"></i>
                            <span>{{ $item->title }}</span>
                        </a>
                        <div class="content"></div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
