@extends(config('laravolt.auth.layout'))

@section('content')

    @if (session('status'))
        <?php flash()->success(session('status')); ?>
    @endif

    <h3 class="ui header">@lang('laravolt::auth.reset_password')</h3>

    <form class="ui form" method="POST" action="{{ route('auth::reset', $token) }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="ui field fluid">
            <input type="email" name="email" placeholder="@lang('laravolt::auth.email')" value="{{ old('email', $email) }}">
        </div>
        <div class="ui field fluid">
            <input type="password" name="password" placeholder="@lang('laravolt::auth.password_new')">
        </div>
        <div class="ui field fluid">
            <input type="password" name="password_confirmation" placeholder="@lang('laravolt::auth.password_confirm')">
        </div>
        <button type="submit" class="ui fluid button primary">@lang('laravolt::auth.reset_password')</button>
    </form>

    <div class="ui divider hidden section"></div>
    @lang('laravolt::auth.already_registered?') <a href="{{ route('auth::login') }}">@lang('laravolt::auth.login_here')</a>
@endsection
