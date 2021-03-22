<x-laravolt::layout.auth>

    <h3 class="ui header horizontal divider section">@lang('laravolt::auth.register')</h3>

    {!! form()->open() !!}
    {!! form()->text('name')->label(__('laravolt::auth.name')) !!}
    {!! form()->email('email')->label(__('laravolt::auth.identifier')) !!}
    {!! form()->password('password')->label(__('laravolt::auth.password')) !!}
    {!! form()->password('password_confirmation')->label(__('laravolt::auth.password_confirmation')) !!}
    <div class="field action">
        <x-laravolt::button class="fluid">@lang('laravolt::auth.register')</x-laravolt::button>
    </div>

    <div class="ui divider section"></div>
    <div>
        @lang('laravolt::auth.already_registered?')
        <a themed href="{{ route('auth::login.show') }}" class="link">@lang('laravolt::auth.login_here')</a>
    </div>

    {!! form()->close() !!}

</x-laravolt::layout.auth>>
