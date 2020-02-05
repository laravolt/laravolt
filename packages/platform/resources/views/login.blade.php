@extends(config('laravolt.auth.layout'))

@section('content')
    <h2 class="ui header brand left aligned">
        <img src="{{ config('laravolt.ui.brand_image') }}" alt="" class="ui image">
        <span class="brand-name content">{{ config('laravolt.ui.brand_name') }}</span>
    </h2>

    <div class="text-uppercase login-text center aligned">@lang('laravolt::auth.login')</div>

    <form class="ui form" method="POST" action="{{ route('auth::login') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="field">
            <label for="">@lang('laravolt::auth.identifier')</label>
            <div class="ui field right icon input fluid">
                <input type="text" name="{{ config('laravolt.auth.identifier') }}" placeholder="@lang('laravolt::auth.identifier')" value="{{ old(config('laravolt.auth.identifier')) }}">
                <i class="at icon"></i>
            </div>
        </div>
        <div class="field">
            <label for="">@lang('laravolt::auth.password')</label>
            <div class="ui field right icon input fluid">
                <input type="password" name="password" placeholder="@lang('laravolt::auth.password')">
                <i class="eye icon"></i>
            </div>
        </div>
        <div class="field left-align">
            <a href="{{ route('auth::forgot') }}" class="link">@lang('laravolt::auth.forgot_password')</a>
        </div>

        @if(config('laravolt.auth.captcha'))
            <div class="field">
                {!! app('captcha')->display() !!}
            </div>
        @endif
        <div class="ui field">
            <button type="submit" class="ui fluid button">@lang('laravolt::auth.login')</button>
        </div>
        <div class="ui equal width grid">
            <div class="column left aligned">
                <div class="ui checkbox">
                    <input type="checkbox" name="remember" {{ request()->old('remember')?'checked':'' }}>
                    <label>@lang('laravolt::auth.remember')</label>
                </div>
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
        <a href="{{ route('auth::register') }}" class="link">@lang('laravolt::auth.register_here')</a>
    @endif

@endsection

@push('script')
    @if(config('laravolt.auth.captcha'))
        {!! app('captcha')->renderJs() !!}
    @endif
@endpush
