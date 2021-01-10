@extends(config('laravolt.auth.layout'))

@section('content')

    <h3 class="ui header horizontal divider m-y-2 m-x-1">@lang('laravolt::auth.register')</h3>

    <form class="ui form" method="POST" action="{{ route('auth::register') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="ui field input fluid big">
            <input type="text" name="name" placeholder="@lang('laravolt::auth.username')" value="{{ old('name') }}">
        </div>
        <div class="ui field input fluid big">
            <input type="email" name="email" placeholder="@lang('laravolt::auth.email')" value="{{ old('email') }}">
        </div>
        <div class="ui field input fluid big">
            <input type="password" name="password" placeholder="@lang('laravolt::auth.password')">
        </div>
        <div class="ui field input fluid big">
            <input type="password" name="password_confirmation" placeholder="@lang('laravolt::auth.password_confirmation')">
        </div>
        <button type="submit" class="ui button fluid primary  big">@lang('laravolt::auth.register')</button>
    </form>

    <div class="ui divider hidden section"></div>
    @lang('laravolt::auth.already_registered?') <a href="{{ route('auth::login') }}" class="link">@lang('laravolt::auth.login_here')</a>

@endsection
