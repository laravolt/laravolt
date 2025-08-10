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
            <button type="submit" class="mt-4 w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">@lang('Kirim Ulang Email Verifikasi')</button>
        {!! form()->close() !!}

        <div class="mt-4">
            <a href="{{ route('auth::logout') }}" class="w-full inline-flex justify-center py-2.5 px-4 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">Logout</a>
        </div>
    </div>
</x-volt-auth>
