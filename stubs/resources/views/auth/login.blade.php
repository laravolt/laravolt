<x-volt-auth>
    <h3 class="text-xl font-semibold text-center text-gray-800 dark:text-neutral-200 mb-4">@lang('laravolt::auth.login')</h3>

    {!! form()->open(route('auth::login.store'))->attribute('up-target', 'body') !!}
        {!! form()->email('email')->label(__('laravolt::auth.identifier')) !!}
        {!! form()->password('password')->label(__('laravolt::auth.password')) !!}

        @if(config('laravolt.platform.features.captcha'))
            <div class="field">
                {!! app('captcha')->display() !!}
                {!! app('captcha')->renderJs() !!}

            </div>
        @endif

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-x-2 text-sm text-gray-700 dark:text-neutral-300">
                    <input type="checkbox" name="remember" class="shrink-0 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ request()->old('remember')?'checked':'' }}>
                    <span>@lang('laravolt::auth.remember')</span>
                </label>
                <a themed href="{{ route('auth::forgot.show') }}" class="text-sm text-blue-600 hover:underline">@lang('laravolt::auth.forgot_password')</a>
            </div>
        </div>

        <div>
            <x-volt-button class="w-full">@lang('laravolt::auth.login')</x-volt-button>
        </div>

        @if(config('laravolt.platform.features.registration'))
            <div class="my-4 h-px bg-gray-200 dark:bg-neutral-700"></div>
            <div>
                @lang('laravolt::auth.not_registered_yet?')
                <a themed href="{{ route('auth::registration.show') }}"
                class="link">@lang('laravolt::auth.register_here')</a>
            </div>
        @endif
    {!! form()->close() !!}

</x-volt-auth>
