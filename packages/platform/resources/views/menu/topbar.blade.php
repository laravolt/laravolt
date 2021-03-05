<header class="ui menu fixed top borderless">
    <div class="item mobile only tablet only" data-role="sidebar-visibility-switcher"><i class="icon sidebar"></i></div>

    @yield('page.back')

    <div class="menu" id="titlebar">
        <div class="item">
            <div class="ui huge breadcrumb">
                <a class="section">
                    <img
                            style="height: 25px"
                            src="{{ config('laravolt.ui.brand_image') }}"
                            alt=""
                    >
                </a>
                <i class="right chevron icon divider"></i>
                <a class="section">Registration</a>
                <i class="right chevron icon divider"></i>
                <div class="active section">Personal Information</div>
            </div>
        </div>
        <div class="item">
        </div>

        @yield('page.actions')
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
