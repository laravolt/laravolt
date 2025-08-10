<x-volt-auth>
    <h3 class="text-xl font-semibold text-center text-gray-800 dark:text-neutral-200 mb-4">@lang('Verifikasi Email')</h3>

    <div class="rounded-lg border border-blue-200 bg-blue-50 text-blue-800 p-3">
        <strong>{{ __('Anda sudah terdaftar di aplikasi. ') }}</strong>
        <p>{{ __('Sebelum bisa melanjutkan, silakan verifikasi akun Anda dengan mengklik link yang kami kirimkan ke alamat email :email.', ['email' => auth()->user()->getEmailForVerification()]) }}</p>
        <p>@lang('Jika Anda belum menerima email, silakan klik tombol di bawah ini.')</p>
    </div>

    {!! form()->post(route('verification.send')) !!}
        <x-volt-button class="fluid">@lang('Kirim Ulang Email Verifikasi')</x-volt-button>
    {!! form()->close() !!}

    <div class="my-4 h-px bg-gray-200 dark:bg-neutral-700"></div>
    <x-volt-link :url="route('auth::logout')" class="fluid">Logout</x-volt-link>
</x-volt-auth>
