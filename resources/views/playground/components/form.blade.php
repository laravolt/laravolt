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
    <h3 class="ui divider horizontal section">Chained Dropdown</h3>
    <p class="ui message">Silakan pilih salah satu user, makan dropdown kedua akan otomatis ter-update dengan
        menampilkan daftar user yang berhubungan</p>
    {!! form()->dropdownDB('user1', 'select id, email as name from users')->label('User') !!}
    {!! form()->dropdownDB('user2', 'select id, email as name from users where id = %s')->label('Similar User')->dependency('user1') !!}
    <h3 class="ui divider horizontal section">Dropdown With Remove Content</h3>
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

<div class="ui grid equal width">
    <div class="column">
        <x-volt-panel title="Horizontal Form">
            {!! form()->get()->horizontal() !!}
            <h3 class="ui horizontal divider section">Basic Info</h3>
            {!! form()->text('nama')->label('Nama') !!}
            {!! form()->dropdown('lokasi', ['Indonesia', 'Malaysia'])->label('Lokasi') !!}
            <h3 class="ui horizontal divider section">Localization</h3>
            {!! form()->dropdown('language', ['Indonesia', 'Malaysia'])->label('Language') !!}
            {!! form()->dropdown('timezone', ['Indonesia', 'Malaysia'])->label('Timezone') !!}
            {!! form()->action(form()->submit('Simpan')) !!}
            {!! form()->close() !!}
        </x-volt-panel>
    </div>
    <div class="column">
        <x-volt-panel title="Vertical Form">
            {!! form()->get() !!}
            <h3 class="ui horizontal divider section">Basic Info</h3>
            {!! form()->text('nama')->label('Nama') !!}
            {!! form()->dropdown('lokasi', ['Indonesia', 'Malaysia'])->label('Lokasi') !!}
            <h3 class="ui horizontal divider section">Localization</h3>
            {!! form()->dropdown('language', ['Indonesia', 'Malaysia'])->label('Language') !!}
            {!! form()->dropdown('timezone', ['Indonesia', 'Malaysia'])->label('Timezone') !!}
            {!! form()->submit('Simpan') !!}
            {!! form()->action(form()->close()) !!}
        </x-volt-panel>
    </div>
</div>
