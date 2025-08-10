<x-volt-auth>
    <h3 class="text-xl font-semibold text-center text-gray-800 dark:text-neutral-200 mb-4">@lang('laravolt::auth.forgot_password')</h3>

    {!! form()->open(route('auth::forgot.store')) !!}
    {!! form()->email('email')->label(__('laravolt::auth.email')) !!}

    <div>
        <x-volt-button class="w-full">@lang('laravolt::auth.send_reset_password_link')</x-volt-button>
    </div>

    @if(config('laravolt.platform.features.registration'))
        <div class="my-4 h-px bg-gray-200 dark:bg-neutral-700"></div>
        @lang('laravolt::auth.not_registered_yet?')
        <a themed href="{{ route('auth::registration.show') }}" class="link">@lang('laravolt::auth.register_here')</a>
    @endif
    {!! form()->close() !!}
</x-volt-auth>
