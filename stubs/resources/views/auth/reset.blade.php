<x-volt-auth>
    <h3 class="ui header horizontal divider section">@lang('laravolt::auth.reset_password')</h3>

    {!! form()->open(route('auth::reset.store', $token)) !!}
    {!! form()->hidden('token', $token) !!}
    {!! form()->email('email', request('email'))->label(__('laravolt::auth.email'))->required() !!}
    {!! form()->password('password')->label(__('laravolt::auth.password_new'))->required() !!}
    {!! form()->password('password_confirmation')->label(__('laravolt::auth.password_confirm'))->required() !!}
    {!! form()->action(form()->submit(__('laravolt::auth.reset_password'))) !!}
    {!! form()->close() !!}

    <div class="ui divider section"></div>

    @lang('laravolt::auth.already_registered?')

    <a href="{{ route('auth::login.show') }}">@lang('laravolt::auth.login_here')</a>
</x-volt-auth>
