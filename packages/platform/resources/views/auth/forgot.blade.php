@extends(config('laravolt.auth.layout'))

@section('content')

    @if (session('status'))
        <?php flash()->success(session('status')); ?>
    @endif

    <h3 class="ui header horizontal divider m-y-2 m-x-1">@lang('laravolt::auth.forgot_password')</h3>

    <form class="ui form" method="POST" action="{{ route('auth::forgot') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="ui field right icon input fluid big">
            <input type="email" name="email" placeholder="@lang('laravolt::auth.email')" value="{{ old('email') }}">
            <i class="mail icon"></i>
        </div>
        <button type="submit" class="ui fluid button big primary">@lang('laravolt::auth.send_reset_password_link')</button>
    </form>

    @if(config('laravolt.auth.registration.enable'))
        <div class="ui divider hidden section"></div>
        @lang('laravolt::auth.not_registered_yet?') <a href="{{ route('auth::register') }}" class="link">@lang('laravolt::auth.register_here')</a>
    @endif
@endsection
