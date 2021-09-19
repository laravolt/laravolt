<x-volt-auth>
    <h3 class="ui header horizontal divider section">@lang('laravolt::auth.register')</h3>

    {!! form()->open(route('auth::registration.store')) !!}
        {!! form()->text('name')->label(__('Name')) !!}
        {!! form()->email('email')->label(__('Email')) !!}
        {!! form()->password('password')->label(__('Password')) !!}
        {!! form()->password('password_confirmation')->label(__('Confirm Your Password')) !!}

        <div class="field action">
            <x-volt-button class="fluid">@lang('laravolt::auth.register')</x-volt-button>
        </div>

        <div class="ui divider section"></div>

        <div>
            @lang('laravolt::auth.already_registered?')
            <a themed href="{{ route('auth::login.show') }}" class="link">@lang('laravolt::auth.login_here')</a>
        </div>
    {!! form()->close() !!}
</x-volt-auth>>
