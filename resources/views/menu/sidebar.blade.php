@php($items = app('laravolt.menu.sidebar')->all())

<nav class="sidebar" data-role="sidebar" id="sidebar">
    <script>
        if (document.body.clientWidth < 991 || localStorage.getItem('layout-mode') === 'full') {
            document.getElementById('sidebar').classList.add('hide');
            document.getElementById('topbar').classList.add('full');
        } else {
            document.getElementById('sidebar').classList.add('show');
        }

    </script>
    <div class="sidebar__scroller">

        <div class="sidebar__menu">

            <div class="sidebar__logo p-2">
                <x-volt-brand-image></x-volt-brand-image>
            </div>

            @auth
                <div class="sidebar__profile">
                    <img src="{{ auth()->user()->avatar }}" class="ui image">
                    <div class="meta">
                        <h4 class="ui header">{{ auth()->user()->name }} panjang sekali</h4>
                        <div class="extra">
                            <a href="{{ route('auth::logout') }}" class="item">Logout</a>
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
                            <div class="ui accordion sidebar__accordion m-b-1" data-role="sidenav">
                                @include('laravolt::menu.sidebar_items', ['items' => $item->children()])
                            </div>
                        @else
                            <div class="ui accordion sidebar__accordion">
                                <a class="title title__1 empty {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) }}"
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
