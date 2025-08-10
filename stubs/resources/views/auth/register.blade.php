<x-volt-auth>
    <div class="text-center">
        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">@lang('laravolt::auth.register')</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
            @lang('laravolt::auth.already_registered?')
            <a class="text-blue-600 decoration-2 hover:underline focus:outline-hidden focus:underline font-medium dark:text-blue-500" href="{{ route('auth::login.show') }}">
                @lang('laravolt::auth.login_here')
            </a>
        </p>
    </div>

    <div class="mt-5">
        {!! form()->open(route('auth::registration.store')) !!}
            <div class="grid gap-y-4">
                {!! form()->text('name')->label(__('Name')) !!}
                {!! form()->email('email')->label(__('Email')) !!}
                {!! form()->password('password')->label(__('Password')) !!}
                {!! form()->password('password_confirmation')->label(__('Confirm Your Password')) !!}

                <x-volt-button type="submit">@lang('laravolt::auth.register')</x-volt-button>
            </div>
        {!! form()->close() !!}
    </div>
</x-volt-auth>
