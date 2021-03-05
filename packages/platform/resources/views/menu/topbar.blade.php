<header class="ui menu fixed top borderless">
    <div class="item mobile only tablet only" data-role="sidebar-visibility-switcher"><i class="icon sidebar"></i></div>

    @yield('page.back')

    <div class="menu p-l-2" id="titlebar">
        <div class="left menu">
            <div class="item">
                <img
                        style="height: 25px"
                        class="ui image m-r-1"
                        src="{{ config('laravolt.ui.brand_image') }}"
                        alt=""
                >
                {{ config('laravolt.ui.brand_name') }}
                <div class="ui label violet small">V 1.2.3</div>
            </div>
            {{--<div class="item divider">--}}
            {{--    <svg width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
            {{--        <path d="M15.5 0L25 18.9429L15.5 39.5"--}}
            {{--              style="fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1;"/>--}}
            {{--    </svg>--}}
            {{--</div>--}}
            {{--<div class="item">--}}
            {{--    <a href="#">Section</a>--}}
            {{--</div>--}}
            {{--<div class="item divider">--}}
            {{--    <svg width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
            {{--        <path d="M15.5 0L25 18.9429L15.5 39.5"--}}
            {{--              style="fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1;"/>--}}
            {{--    </svg>--}}
            {{--</div>--}}
            {{--<div class="item">--}}
            {{--    <a href="#">Page</a>--}}
            {{--</div>--}}
        </div>

{{--        @yield('page.actions')--}}
    </div>

    <div class="menu right p-r-1" id="userbar" data-turbolinks-permanent>
        @auth
            <div class="item">
                <div class="ui compact menu b-0">
                    <div class="ui simple dropdown basic button top right pointing b-0 p-x-0">
                        <img src="{{ auth()->user()->avatar }}" alt="" class="ui image avatar">
                        <i class="dropdown icon m-l-0 {{ config('laravolt.ui.color') }}"></i>
                        <div class="menu">
                            <div class="header"><span class="ui text {{ config('laravolt.ui.color') }}">{{ auth()->user()->name }}</span></div>

                            <div class="divider"></div>

                            <a href="{{ route('epicentrum::my.profile.edit') }}" class="item">@lang('Edit Profil')</a>
                            <a href="{{ route('epicentrum::my.password.edit') }}" class="item">@lang('Edit Password')</a>

                            <div class="divider"></div>

                            <a href="{{ route('auth::logout') }}" class="item">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        @endauth

    </div>
</header>
