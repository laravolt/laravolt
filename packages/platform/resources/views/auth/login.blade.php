@extends(config('laravolt.auth.layout'))

@section('content')
    <h3 class="ui header horizontal divider section">@lang('laravolt::auth.login')</h3>

    {!! form()->open(route('auth::login.action')) !!}
    {!! form()->email(config('laravolt.auth.identifier'))->label(__('laravolt::auth.identifier')) !!}
    {!! form()->password('password')->label(__('laravolt::auth.password')) !!}

    @if(config('laravolt.auth.captcha'))
        <div class="field">
            {!! app('captcha')->display() !!}
        </div>
    @endif

    <div class="ui field m-b-2">
        <div class="ui equal width grid">
            <div class="column left aligned">
                <div class="ui checkbox">
                    <input type="checkbox" name="remember" {{ request()->old('remember')?'checked':'' }}>
                    <label>@lang('laravolt::auth.remember')</label>
                </div>
            </div>
            <div class="column right aligned">
                <a themed href="{{ route('auth::forgot') }}" class="link">@lang('laravolt::auth.forgot_password')</a>
            </div>
        </div>
    </div>

    <div class="field action">
        <x-button class="fluid">@lang('laravolt::auth.login')</x-button>
    </div>

    @if(config('laravolt.auth.registration.enable'))
        <div>
            @lang('laravolt::auth.not_registered_yet?')
            <a themed href="{{ route('auth::register') }}" class="link">@lang('laravolt::auth.register_here')</a>
        </div>
    @endif

    {!! form()->close() !!}

@endsection

@push('script')
    @if(config('laravolt.auth.captcha'))
        {!! app('captcha')->renderJs() !!}
    @endif
@endpush