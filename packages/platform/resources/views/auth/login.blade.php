@extends(config('laravolt.auth.layout'))

@section('content')
    <h3 class="ui header horizontal divider m-y-2 m-x-1">@lang('laravolt::auth.login')</h3>

    <form class="ui form" method="POST" action="{{ route('auth::login.action') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="field">
            <div class="ui field right icon input fluid big">
                <input type="text" name="{{ config('laravolt.auth.identifier') }}" placeholder="@lang('laravolt::auth.identifier')" value="{{ old(config('laravolt.auth.identifier')) }}">
                <i class="at icon"></i>
            </div>
        </div>
        <div class="field">
            <div class="ui field right icon input fluid big">
                <input type="password" name="password" placeholder="@lang('laravolt::auth.password')">
                <i class="lock icon"></i>
            </div>
        </div>

        @if(config('laravolt.auth.captcha'))
            <div class="field">
                {!! app('captcha')->display() !!}
            </div>
        @endif
        <div class="ui field">
            <x-button class="big fluid">@lang('laravolt::auth.login')</x-button>
        </div>
        <div class="ui equal width grid">
            <div class="column left aligned">
                <div class="ui checkbox">
                    <input type="checkbox" name="remember" {{ request()->old('remember')?'checked':'' }}>
                    <label>@lang('laravolt::auth.remember')</label>
                </div>
            </div>
            <div class="column right aligned">
                <a href="{{ route('auth::forgot') }}" class="link">@lang('laravolt::auth.forgot_password')</a>
            </div>
        </div>

    </form>

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
