<x-volt-auth :title="__('Forgot Password Page')">
    <div class="text-center">
        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">@lang('laravolt::auth.forgot_password')</h1>
        @if (config('laravolt.platform.features.registration'))
            <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
                @lang('laravolt::auth.not_registered_yet?')
                <a class="text-blue-600 decoration-2 hover:underline focus:outline-hidden focus:underline font-medium dark:text-blue-500"
                    href="{{ route('auth::registration.show') }}">
                    @lang('laravolt::auth.register_here')
                </a>
            </p>
        @endif
    </div>

    <div class="mt-5">
        {!! form()->open(route('auth::forgot.store')) !!}
            <div class="py-2 sm:py-4">
                <div class="space-y-5">
                    {!! form()->email('email')->label(__('laravolt::auth.email')) !!}
                </div>
            </div>

            <div class="pt-0 flex justify-center gap-x-2">
                <div class="w-full flex justify-center items-center gap-x-2">
                    <x-volt-button class="fluid">
                        @lang('laravolt::auth.send_reset_password_link')
                    </x-volt-button>
                </div>
            </div>
        {!! form()->close() !!}
    </div>
</x-volt-auth>
