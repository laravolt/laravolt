@component('laravolt::mail.body')
    @component('laravolt::mail.headline')
        Aktivasi Akun
    @endcomponent

    @component('laravolt::mail.message')
        Untuk melakukan aktivasi akun, silakan klik tombol di bawah ini:
    @endcomponent


    @component('laravolt::mail.button', ['url' => route('auth::activate', $token)])
        Aktivasi Akun
    @endcomponent

    @component('laravolt::mail.info')
        Jika Anda tidak merasa melakukan pendaftaran di <strong>{{ config('app.url') }}</strong>, abaikan email ini.
    @endcomponent

@endcomponent
