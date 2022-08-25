<x-volt-auth>
    <h3 class="ui header horizontal divider section">@lang('laravolt::auth.forgot_password')</h3>

    {!! form()->open(route('auth::forgot.store')) !!}
    {!! form()->email('email')->label(__('laravolt::auth.email')) !!}

    <div class="field action">
        <x-volt-button class="fluid">@lang('laravolt::auth.send_reset_password_link')</x-volt-button>
    </div>

    @if(config('laravolt.platform.features.registration'))
        <div class="ui divider section"></div>
        @lang('laravolt::auth.not_registered_yet?')
        <a themed href="{{ route('auth::registration.show') }}" class="link">@lang('laravolt::auth.register_here')</a>
    @endif
    {!! form()->close() !!}
</x-volt-auth>>
