<x-volt-auth :title="__('Login Page')">
    <div class="text-center">
        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">@lang('laravolt::auth.login')</h1>
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
        {!! form()->open(route('auth::login.store')) !!}
            <div class="py-2 sm:py-4">
                <div class="space-y-5">
                    {!! form()->email('email')->label(__('laravolt::auth.identifier')) !!}
                    {!! form()->password('password')->label(__('laravolt::auth.password')) !!}

                    <!-- Remember -->
                    <div class="flex items-center">
                        <div class="flex">
                            <input id="remember" name="remember" type="checkbox"
                                class="shrink-0 mt-0.5 border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                                {{ old('remember') ? 'checked' : '' }}>
                        </div>
                        <div class="ms-3">
                            <label for="remember" class="text-sm dark:text-white">@lang('laravolt::auth.remember')</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-0 flex justify-center gap-x-2">
                <div class="w-full flex justify-center items-center gap-x-2">
                    <x-volt-button type="submit">@lang('laravolt::auth.login')</x-volt-button>
                </div>
            </div>
        {!! form()->close() !!}

        @if (config('laravolt.platform.features.registration'))
            <div class="pt-0 flex justify-center gap-x-2">
                <div class="w-full flex justify-center items-center gap-x-2">
                    <p class="mt-6 text-sm text-gray-600 dark:text-neutral-400">
                        <a class="text-blue-600 decoration-2 hover:underline focus:outline-hidden focus:underline font-medium dark:text-blue-500"
                            href="{{ route('auth::forgot.show') }}">
                            @lang('laravolt::auth.forgot_password')
                        </a>
                    </p>
                </div>
            </div>
        @endif
    </div>
</x-volt-auth>
