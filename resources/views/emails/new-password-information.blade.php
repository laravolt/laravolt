@component('laravolt::mail.body')
    @component('laravolt::mail.headline')
        Perubahan Password
    @endcomponent

    @component('laravolt::mail.message')
        Admin telah mengubah password Anda menjadi:
        <br>
        <h3 style="font-size: 72px; font-family: serif; font-weight: 400">{{ $password }}</h3>
        Silakan gunakan password baru tersebut untuk login di <strong>{{ config('app.url') }}</strong>.
    @endcomponent
@endcomponent
