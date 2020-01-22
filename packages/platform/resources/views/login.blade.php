@extends(config('laravolt.auth.layout'))

@section('content')

    <h3 class="text-uppercase m-b-1">@lang('laravolt::auth.login')</h3>

    <form class="ui form" method="POST" action="{{ route('auth::login') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="ui field left icon input fluid">
            <input type="text" name="{{ config('laravolt.auth.identifier') }}" placeholder="@lang('laravolt::auth.identifier')" value="{{ old(config('laravolt.auth.identifier')) }}">
            <i class="mail icon"></i>
        </div>
        <div class="ui field left icon input fluid">
            <input type="password" name="password" placeholder="@lang('laravolt::auth.password')">
            <i class="lock icon"></i>
        </div>
        @if(config('laravolt.auth.captcha'))
            <div class="field">
                {!! app('captcha')->display() !!}
            </div>
        @endif
        <div class="ui field">
            <button type="submit" class="ui fluid button primary">@lang('laravolt::auth.login')</button>
        </div>
        <div class="ui equal width grid">
            <div class="column left aligned">
                <div class="ui checkbox">
                    <input type="checkbox" name="remember" {{ request()->old('remember')?'checked':'' }}>
                    <label>@lang('laravolt::auth.remember')</label>
                </div>
            </div>
            <div class="column right aligned">
                <a href="{{ route('auth::forgot') }}">@lang('laravolt::auth.forgot_password')</a>
            </div>
        </div>

    </form>

    @if(config('laravolt.auth.cas.enable'))
        <div class="ui horizontal divider">
            Or
        </div>
        <a href="{{ route('auth::cas.login') }}" class="ui fluid button basic">@lang('laravolt::auth.login_cas')</a>
    @endif

    @if(config('laravolt.auth.registration.enable'))
        <div class="ui divider hidden section"></div>
        @lang('laravolt::auth.not_registered_yet?')
        <a href="{{ route('auth::register') }}">@lang('laravolt::auth.register_here')</a>
    @endif

@endsection

@push('script')
    @if(config('laravolt.auth.captcha'))
        {!! app('captcha')->renderJs() !!}
    @endif
@endpush
