@component('laravolt::mail.body')
    @component('laravolt::mail.headline')
        Reset Password
    @endcomponent

    @component('laravolt::mail.message')
        Anda baru saja melakukan
        <br> permintaan reset password di <strong>{{ config('app.url') }}</strong>.
        <br> Untuk melanjutkan proses, silakan klik tombol di bawah ini:
    @endcomponent

    @component('laravolt::mail.button', ['url' => route('auth::reset', compact('token', 'email'))])
        Reset Password
    @endcomponent

    @component('laravolt::mail.info')
        Jika Anda tidak merasa melakukan permintaan reset password, abaikan email ini.
    @endcomponent

@endcomponent
