<x-volt-auth :title="__('Register Page')">
    <div class="text-center">
        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">@lang('laravolt::auth.register')</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
            @lang('laravolt::auth.already_registered?')
            <a class="text-blue-600 decoration-2 hover:underline focus:outline-hidden focus:underline font-medium dark:text-blue-500"
                href="{{ route('auth::login.show') }}">
                @lang('laravolt::auth.login_here')
            </a>
        </p>
    </div>

    <div class="mt-5">
        {!! form()->open(route('auth::registration.store')) !!}
            <div class="py-2 sm:py-4">
                <div class="space-y-5">
                    {!! form()->text('name')->label(__('Name')) !!}
                    {!! form()->email('email')->label(__('Email')) !!}
                    {!! form()->password('password')->label(__('Password')) !!}
                    {!! form()->password('password_confirmation')->label(__('Confirm Your Password')) !!}
                </div>
            </div>

            <div class="pt-0 flex justify-center gap-x-2">
                <div class="w-full flex justify-center items-center gap-x-2">
                    <x-volt-button class="fluid">@lang('laravolt::auth.register')</x-volt-button>
                </div>
            </div>
        {!! form()->close() !!}
    </div>
</x-volt-auth>
