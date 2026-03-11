<x-volt-panel title="Form Fields">
    {!! form()->open(route('platform::dump'))->multipart() !!}
    {!! form()->text('text')->label('Text') !!}
    {!! form()->email('email')->label('Email') !!}
    {!! form()->number('number')->label('Number') !!}
    {!! form()->password('password')->label('Password') !!}
    {!! form()->color('color')->label('Color') !!}
    {!! form()->time('time')->label('Time') !!}
    {!! form()->date('date')->label('Date') !!}
    {!! form()->datepicker('datepicker')->label('Datepicker') !!}
    {!! form()->rupiah('rupiah1')->label('Rupiah') !!}
    {!! form()->uploader('avatar')->label('Single File Upload') !!}
    {!! form()->uploader('attachments')->limit(10)->label('Multiple File Upload') !!}
    {!! form()->uploader('attachments_non_ajax')->ajax(false)->limit(10)->label('Multiple File Upload Wihout Ajax') !!}

    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200 dark:border-neutral-700"></div></div>
        <div class="relative flex justify-center"><span class="bg-white px-3 text-sm font-medium text-gray-500 dark:bg-neutral-800 dark:text-neutral-400">Chained Dropdown</span></div>
    </div>

    <div class="rounded-lg border border-blue-200 bg-blue-50 p-3 mb-4 dark:bg-blue-900/50 dark:border-blue-800">
        <p class="text-sm text-blue-700 dark:text-blue-300">Silakan pilih salah satu user, makan dropdown kedua akan otomatis ter-update dengan
        menampilkan daftar user yang berhubungan</p>
    </div>
    {!! form()->dropdownDB('user1', 'select id, email as name from users')->label('User') !!}
    {!! form()->dropdownDB('user2', 'select id, email as name from users where id = %s')->label('Similar User')->dependency('user1') !!}

    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200 dark:border-neutral-700"></div></div>
        <div class="relative flex justify-center"><span class="bg-white px-3 text-sm font-medium text-gray-500 dark:bg-neutral-800 dark:text-neutral-400">Dropdown With Remove Content</span></div>
    </div>

    {!! form()->dropdownDB('user3', 'select id, email as name from users where email like "%%%s%%" limit 10')->prependOption(2, 'Foo')->ajax()->label('Search User') !!}
    {!! form()->coordinate('koordinat')->label('Koordinat') !!}
    {!! form()->redactor('redactor')->label('WYSIWYG (Redactor)') !!}
    {!! form()->submit('Submit') !!}
    {!! form()->close() !!}
</x-volt-panel>
<x-volt-panel title="Field Hints">
    {!! form()->get()->horizontal() !!}
    {!! form()->text('field1')->label('Username')->hint('Minimal 6 karakter.')->hint('Hanya boleh huruf dan angka.') !!}
    {!! form()->password('password')->label('Password')->hint('Minimal 8 karakter.')->hint('Harus mengandung huruf, angka, dan karakter aneh.') !!}
    {!! form()->close() !!}
</x-volt-panel>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-volt-panel title="Horizontal Form">
            {!! form()->get()->horizontal() !!}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200 dark:border-neutral-700"></div></div>
                <div class="relative flex justify-center"><span class="bg-white px-3 text-sm font-medium text-gray-500 dark:bg-neutral-800 dark:text-neutral-400">Basic Info</span></div>
            </div>
            {!! form()->text('nama')->label('Nama') !!}
            {!! form()->dropdown('lokasi', ['Indonesia', 'Malaysia'])->label('Lokasi') !!}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200 dark:border-neutral-700"></div></div>
                <div class="relative flex justify-center"><span class="bg-white px-3 text-sm font-medium text-gray-500 dark:bg-neutral-800 dark:text-neutral-400">Localization</span></div>
            </div>
            {!! form()->dropdown('language', ['Indonesia', 'Malaysia'])->label('Language') !!}
            {!! form()->dropdown('timezone', ['Indonesia', 'Malaysia'])->label('Timezone') !!}
            {!! form()->action(form()->submit('Simpan')) !!}
            {!! form()->close() !!}
        </x-volt-panel>
    </div>
    <div>
        <x-volt-panel title="Vertical Form">
            {!! form()->get() !!}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200 dark:border-neutral-700"></div></div>
                <div class="relative flex justify-center"><span class="bg-white px-3 text-sm font-medium text-gray-500 dark:bg-neutral-800 dark:text-neutral-400">Basic Info</span></div>
            </div>
            {!! form()->text('nama')->label('Nama') !!}
            {!! form()->dropdown('lokasi', ['Indonesia', 'Malaysia'])->label('Lokasi') !!}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200 dark:border-neutral-700"></div></div>
                <div class="relative flex justify-center"><span class="bg-white px-3 text-sm font-medium text-gray-500 dark:bg-neutral-800 dark:text-neutral-400">Localization</span></div>
            </div>
            {!! form()->dropdown('language', ['Indonesia', 'Malaysia'])->label('Language') !!}
            {!! form()->dropdown('timezone', ['Indonesia', 'Malaysia'])->label('Timezone') !!}
            {!! form()->submit('Simpan') !!}
            {!! form()->close() !!}
        </x-volt-panel>
    </div>
</div>
