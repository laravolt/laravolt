<x-laravolt::layout.auth>
    <h3 class="ui header horizontal divider section">@lang('Verifikasi Email')</h3>

    <div class="ui message p-3">
        <strong>{{ __('Anda sudah terdaftar di aplikasi. ') }}</strong>
        <p>{{ __('Sebelum bisa melanjutkan, silakan verifikasi akun Anda dengan mengklik link yang kami kirimkan ke alamat email :email.', ['email' => auth()->user()->getEmailForVerification()]) }}</p>
        <p>@lang('Jika Anda belum menerima email, silakan klik tombol di bawah ini.')</p>
    </div>

    {!! form()->post(route('verification.send')) !!}
        <x-laravolt::button class="fluid">@lang('Kirim Ulang Email Verifikasi')</x-laravolt::button>
    {!! form()->close() !!}

    <div class="ui divider section"></div>

    <x-laravolt::link :url="route('auth::logout')" class="fluid">Logout</x-laravolt::link>
</x-laravolt::layout.auth>
