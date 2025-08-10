<x-volt-auth>
    <h3 class="text-xl font-semibold text-center text-gray-800 dark:text-neutral-200 mb-4">@lang('laravolt::auth.register')</h3>

    {!! form()->open(route('auth::registration.store')) !!}
        {!! form()->text('name')->label(__('Name')) !!}
        {!! form()->email('email')->label(__('Email')) !!}
        {!! form()->password('password')->label(__('Password')) !!}
        {!! form()->password('password_confirmation')->label(__('Confirm Your Password')) !!}

        <div>
            <x-volt-button class="w-full">@lang('laravolt::auth.register')</x-volt-button>
        </div>

        <div class="my-4 h-px bg-gray-200 dark:bg-neutral-700"></div>

        <div>
            @lang('laravolt::auth.already_registered?')
            <a themed href="{{ route('auth::login.show') }}" class="link">@lang('laravolt::auth.login_here')</a>
        </div>
    {!! form()->close() !!}
</x-volt-auth>
