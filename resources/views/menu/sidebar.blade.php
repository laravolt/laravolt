@php($items = app('laravolt.menu.sidebar')->all())

<nav class="sidebar" data-role="sidebar" id="sidebar" data-turbolinks-permanent>
    <div class="sidebar__scroller">

        <div class="sidebar__menu">

            @auth
            <div class="sidebar__profile m-b-0 p-0 p-t-2 ui basic segment text-center">
                <img src="{{ auth()->user()->avatar }}" alt="" class="ui image tiny centered avatar">
                <h4 class="ui header m-b-3">
                    {{ auth()->user()->name }}
                    <div class="sub header">{!!  auth()->user()->roles->map(fn($role) => "{$role->name}")->implode(' &bull; ')  !!}</div>
                </h4>
            </div>
            @endauth

            @if(!$items->isEmpty())
                @if(config('laravolt.platform.features.quick_switcher'))
{{--                    @include('laravolt::quick-switcher.sidebar')--}}
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
                                <a class="title empty {{ \Laravolt\Platform\Services\Menu::setActiveParent($item->children(), $item->isActive) }}"
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
