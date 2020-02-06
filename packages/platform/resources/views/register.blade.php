@extends(config('laravolt.auth.layout'))

@section('content')

    <div class="text-uppercase login-text center aligned">@lang('laravolt::auth.register')</div>

    <form class="ui form" method="POST" action="{{ route('auth::register') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="ui field right icon input fluid">
            <input type="text" name="name" placeholder="@lang('laravolt::auth.name')" value="{{ old('name') }}">
            <i class="user icon"></i>
        </div>
        <div class="ui field right icon input fluid">
            <input type="email" name="email" placeholder="@lang('laravolt::auth.identifier')" value="{{ old('email') }}">
            <i class="mail icon"></i>
        </div>
        <div class="ui field right icon input fluid">
            <input type="password" name="password" placeholder="@lang('laravolt::auth.password')">
            <i class="lock icon"></i>
        </div>
        <div class="ui field right icon input fluid">
            <input type="password" name="password_confirmation" placeholder="@lang('laravolt::auth.password_confirmation')">
            <i class="lock icon"></i>
        </div>
        <button type="submit" class="ui button fluid primary">@lang('laravolt::auth.register')</button>
    </form>

    <div class="ui divider hidden section"></div>
    @lang('laravolt::auth.already_registered?') <a href="{{ route('auth::login') }}" class="link">@lang('laravolt::auth.login_here')</a>

@endsection
