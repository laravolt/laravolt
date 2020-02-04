@extends(config('laravolt.auth.layout'))

@section('content')

    <h3 class="ui header">@lang('laravolt::auth.register')</h3>

    <form class="ui form" method="POST" action="{{ route('auth::register') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="ui field left icon input fluid">
            <input type="text" name="name" placeholder="@lang('laravolt::auth.name')" value="{{ old('name') }}">
            <i class="address card icon"></i>
        </div>
        @if(which_identifier() === 'username' || is_array(config('laravolt.auth.identifier')))
            <div class="ui field left icon input fluid">
                <input type="text" name="username"
                       placeholder="@lang('laravolt::auth.username')"
                       value="{{ old('username') }}">
                <i class="user icon"></i>
            </div>
        @endif
        <div class="ui field left icon input fluid">
            <input type="email" name="email" placeholder="@lang('laravolt::auth.email')" value="{{ old('email') }}">
            <i class="mail icon"></i>
        </div>
        <div class="ui field left icon input fluid">
            <input type="password" name="password" placeholder="@lang('laravolt::auth.password')">
            <i class="lock icon"></i>
        </div>
        <div class="ui field left icon input fluid">
            <input type="password" name="password_confirmation" placeholder="@lang('laravolt::auth.password_confirmation')">
            <i class="lock icon"></i>
        </div>
        <button type="submit" class="ui button fluid primary">@lang('laravolt::auth.register')</button>
    </form>

    <div class="ui divider hidden section"></div>
    @lang('laravolt::auth.already_registered?') <a href="{{ route('auth::login') }}">@lang('laravolt::auth.login_here')</a>

@endsection
