@extends(config('laravolt.auth.layout'))

@section('content')

    <h3 class="ui header horizontal divider section">@lang('laravolt::auth.register')</h3>

    {!! form()->open(route('auth::register.action')) !!}
    {!! form()->text('name')->label(__('laravolt::auth.name')) !!}
    {!! form()->email(config('laravolt.auth.identifier'))->label(__('laravolt::auth.identifier')) !!}
    {!! form()->password('password')->label(__('laravolt::auth.password')) !!}
    {!! form()->password('password_confirmation')->label(__('laravolt::auth.password_confirmation')) !!}
    <div class="field action">
        <x-button class="fluid">@lang('laravolt::auth.register')</x-button>
    </div>

    <div>
        @lang('laravolt::auth.already_registered?')
        <a themed href="{{ route('auth::login') }}" class="link">@lang('laravolt::auth.login_here')</a>
    </div>

    {!! form()->close() !!}

@endsection
