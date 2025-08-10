<x-volt-auth>
    <div class="text-center">
        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">@lang('laravolt::auth.reset_password')</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
            @lang('laravolt::auth.already_registered?')
            <a class="text-blue-600 decoration-2 hover:underline focus:outline-hidden focus:underline font-medium dark:text-blue-500" href="{{ route('auth::login.show') }}">
                @lang('laravolt::auth.login_here')
            </a>
        </p>
    </div>

    <div class="mt-5">
        {!! form()->open(route('auth::reset.store', $token)) !!}
            {!! form()->hidden('token', $token) !!}
            {!! form()->email('email', request('email'))->label(__('Email'))->required() !!}
            {!! form()->password('password')->label(__('New Password'))->required() !!}
            {!! form()->password('password_confirmation')->label(__('Confirm New Password'))->required() !!}

            <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">@lang('Reset Password')</button>
        {!! form()->close() !!}
    </div>
</x-volt-auth>
