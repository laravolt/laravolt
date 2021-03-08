<x-laravolt::layout.auth>

    @if (session('status'))
        <?php flash()->success(session('status')); ?>
    @endif

    <h3 class="ui header horizontal divider section">@lang('laravolt::auth.forgot_password')</h3>

    {!! form()->open()->route('auth::forgot.action') !!}
    {!! form()->email('email')->label(__('laravolt::auth.email')) !!}
    <div class="field action">
        <x-laravolt::button class="fluid">@lang('laravolt::auth.send_reset_password_link')</x-laravolt::button>
    </div>

    @if(config('laravolt.auth.registration.enable'))
        @lang('laravolt::auth.not_registered_yet?')
        <a themed href="{{ route('auth::register') }}" class="link">@lang('laravolt::auth.register_here')</a>
    @endif

    {!! form()->close() !!}

</x-laravolt::layout.auth>>
