<x-volt-auth>
    <div class="text-center">
        <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">@lang('Verifikasi Email')</h1>
    </div>

    <div class="mt-5">
        <div class="rounded-lg border border-gray-200 p-4 text-gray-700 dark:text-neutral-300 dark:border-neutral-700">
            <strong>{{ __('Anda sudah terdaftar di aplikasi. ') }}</strong>
            <p class="mt-2">{{ __('Sebelum bisa melanjutkan, silakan verifikasi akun Anda dengan mengklik link yang kami kirimkan ke alamat email :email.', ['email' => auth()->user()->getEmailForVerification()]) }}</p>
            <p class="mt-2">@lang('Jika Anda belum menerima email, silakan klik tombol di bawah ini.')</p>
        </div>

        {!! form()->post(route('verification.send')) !!}
            <div class="grid gap-y-4 mt-4">
                <x-volt-button type="submit">@lang('Kirim Ulang Email Verifikasi')</x-volt-button>
            </div>
        {!! form()->close() !!}

        <div class="mt-4">
            <a href="{{ route('auth::logout') }}" class="w-full inline-flex justify-center py-2.5 px-4 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">Logout</a>
        </div>
    </div>
</x-volt-auth>
