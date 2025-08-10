<x-volt-auth>
    <h3 class="text-xl font-semibold text-center text-gray-800 dark:text-neutral-200 mb-4">@lang('laravolt::auth.reset_password')</h3>

    {!! form()->open(route('auth::reset.store', $token)) !!}
    {!! form()->hidden('token', $token) !!}
    {!! form()->email('email', request('email'))->label(__('Email'))->required() !!}
    {!! form()->password('password')->label(__('New Password'))->required() !!}
    {!! form()->password('password_confirmation')->label(__('Confirm New Password'))->required() !!}
    {!! form()->action(form()->submit(__('Reset Password'))) !!}
    {!! form()->close() !!}

    <div class="my-4 h-px bg-gray-200 dark:bg-neutral-700"></div>

    @lang('laravolt::auth.already_registered?')

    <a href="{{ route('auth::login.show') }}">@lang('laravolt::auth.login_here')</a>
</x-volt-auth>
