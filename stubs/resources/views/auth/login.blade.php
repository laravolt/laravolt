<x-volt-auth>
    <div class="text-center">
        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">@lang('laravolt::auth.login')</h1>
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
        {!! form()->open(route('auth::login.store')) !!}
            <div class="grid gap-y-4">
                {!! form()->email('email')->label(__('laravolt::auth.identifier')) !!}
                {!! form()->password('password')->label(__('laravolt::auth.password')) !!}

                <!-- Captcha -->
                @if(config('laravolt.platform.features.captcha'))
                    <div>
                        {!! app('captcha')->display() !!}
                        {!! app('captcha')->renderJs() !!}
                    </div>
                @endif

                <!-- Remember -->
                <div class="flex items-center">
                    <div class="flex">
                        <input id="remember" name="remember" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" {{ old('remember') ? 'checked' : '' }}>
                    </div>
                    <div class="ms-3">
                        <label for="remember" class="text-sm dark:text-white">@lang('laravolt::auth.remember')</label>
                    </div>
                </div>

                <x-volt-button type="submit">@lang('laravolt::auth.login')</x-volt-button>
            </div>
        {!! form()->close() !!}
    </div>
</x-volt-auth>
