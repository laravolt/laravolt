@extends(config('laravolt.auth.layout'))

@section('content')
    <h2 class="ui header m-b-3">
        @lang('laravolt::auth.login')
        @if(config('laravolt.auth.registration.enable'))
            <div class="sub header">
                @lang('laravolt::auth.not_registered_yet?')
                <a href="{{ route('auth::register') }}" class="link">@lang('laravolt::auth.register_here')</a>
            </div>
        @endif
    </h2>

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
                <a href="{{ route('auth::forgot') }}" class="link">@lang('laravolt::auth.forgot_password')</a>
            </div>
        </div>
    </div>

    <div class="ui field">
        <x-button class="fluid">@lang('laravolt::auth.login')</x-button>
    </div>

    {!! form()->close() !!}

@endsection

@push('script')
    @if(config('laravolt.auth.captcha'))
        {!! app('captcha')->renderJs() !!}
    @endif
@endpush
