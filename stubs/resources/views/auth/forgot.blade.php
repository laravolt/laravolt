<x-volt-auth>
    <div class="text-center">
        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">@lang('laravolt::auth.forgot_password')</h1>
        @if(config('laravolt.platform.features.registration'))
            <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
                @lang('laravolt::auth.not_registered_yet?')
                <a class="text-blue-600 decoration-2 hover:underline focus:outline-hidden focus:underline font-medium dark:text-blue-500" href="{{ route('auth::registration.show') }}">
                    @lang('laravolt::auth.register_here')
                </a>
            </p>
        @endif
    </div>

    <div class="mt-5">
        {!! form()->open(route('auth::forgot.store')) !!}
            <div class="grid gap-y-4">
                {!! form()->email('email')->label(__('laravolt::auth.email')) !!}

                <x-volt-button type="submit">@lang('laravolt::auth.send_reset_password_link')</x-volt-button>
            </div>
        {!! form()->close() !!}
    </div>
</x-volt-auth>
