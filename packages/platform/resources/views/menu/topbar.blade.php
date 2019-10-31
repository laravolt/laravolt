<header class="ui menu small borderless fixed top {{ config('laravolt.ui.options.topbar_inverted') ? 'inverted': '' }}">
    <div class="item mobile only tablet only" data-role="sidebar-visibility-switcher"><i class="icon sidebar"></i></div>
    {{--<div class="item">--}}
        {{--<div class="ui breadcrumb">--}}
            {{--<a class="section">Home</a>--}}
            {{--<i class="right angle icon divider"></i>--}}
            {{--<a class="section">Store</a>--}}
            {{--<i class="right angle icon divider"></i>--}}
            {{--<div class="active section">T-Shirt</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class="menu right p-r-1">
        {{--<div class="item ui dropdown simple right">--}}
            {{--<i class="icon bell"></i>--}}
            {{--<div class="menu notification">--}}
                {{--<div class="ui comments">--}}
                    {{--<h4 class="ui divider horizontal p-1">Notifikasi</h4>--}}
                    {{--@foreach(range(1,5) as $i)--}}
                        {{--<div class="p-x-1 m-b-1">--}}
                            {{--<a class="comment" href="#">--}}
                                {{--<div class="avatar">--}}
                                    {{--<img src="{{ asset('img/avatar.jpg') }}">--}}
                                {{--</div>--}}
                                {{--<div class="content">--}}
                                    {{--<span>New member joined</span>--}}
                                    {{--<div class="metadata">--}}
                                        {{--<span class="date">Today at 5:42PM</span>--}}
                                    {{--</div>--}}

                                    {{--<div class="text">--}}
                                        {{--Lorem ipsum dolor sit amet, consectetur adipisicing elit.--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</a>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
                {{--<a href="" class="item footer text-center">Lihat Semua</a>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="item">
        </div>

        @auth()
        <div class="ui item dropdown simple right">
            <img src="{{ auth()->user()->avatar }}" alt="" class="ui image avatar">
            {{ auth()->user()->name }}
            <i class="icon dropdown"></i>
            <div class="menu">
                @if(Route::has('epicentrum::my.password.edit'))
                <a href="{{ route('epicentrum::my.password.edit') }}" class="item">Edit Password</a>
                @endif
                <div class="divider"></div>
                <a href="{{ route('auth::logout') }}" class="item">Logout</a>
            </div>
        </div>
        @endauth

    </div>
</header>
