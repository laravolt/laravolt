@php($items = app('laravolt.menu.sidebar')->all())

<nav class="sidebar" data-role="sidebar" id="sidebar">
    <div class="sidebar__scroller">

        <div class="sidebar__menu">

            <div class="sidebar__logo p-2" style="min-height: 124px">
                <x-volt-brand-image></x-volt-brand-image>
            </div>

            @auth
                <div class="sidebar__profile">
                    <div class="ui items">
                        <div class="item">
                            <div class="ui mini image">
                                <img src="{{ auth()->user()->avatar }}">
                            </div>
                            <div class="content">
                                <h3 class="header">{{ auth()->user()->name }}</h3>
                                <div class="extra">
                                    <a href="{{ route('auth::logout') }}" class="item">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth

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
                            @include('laravolt::menu.sidebar_items', ['items' => $item->children()])
                        @else
                            <div class="ui accordion sidebar__accordion">
                                <a class="title empty {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) }}"
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
    </div>
</nav>
