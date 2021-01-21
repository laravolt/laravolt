<div class="ui grid">
    <div class="column">
        <x-panel title="Form Fields">
            {!! form()->open(route('platform::dump'))->multipart() !!}
            {!! form()->text('text')->label('Text') !!}
            {!! form()->email('email')->label('Email') !!}
            {!! form()->number('number')->label('Number') !!}
            {!! form()->date('date')->label('Date') !!}
            {!! form()->time('time')->label('Time') !!}
            {!! form()->rupiah('rupiah1')->label('Rupiah') !!}
            {!! form()->rupiah('rupiah2')->label('Rupiah (dengan koma)') !!}
            {!! form()->uploader('avatar')->label('Single File Upload') !!}
            {!! form()->uploader('attachments')->limit(10)->label('Multiple File Upload') !!}
            <h3 class="ui divider horizontal section">Chained Dropdown</h3>
            <p class="ui message">Silakan pilih salah satu user, makan dropdown kedua akan otomatis ter-update dengan menampilkan daftar user yang berhubungan</p>
            {!! form()->dropdownDB('user1', 'select id, email as name from users')->label('User') !!}
            {!! form()->dropdownDB('user2', 'select id, email as name from users where id = %s')->label('Similar User')->dependency('user1') !!}
            <h3 class="ui divider horizontal section">Dropdown With Remove Content</h3>
            {!! form()->dropdownDB('user3', 'select id, email as name from users where email like "%%%s%%" limit 10')->prependOption(2, 'Foo')->ajax()->label('Search User') !!}
            {!! form()->coordinate('koordinat')->label('Koordinat') !!}
            {!! form()->redactor('redactor')->label('WYSIWYG (Redactor)') !!}
            {!! form()->submit('Submit') !!}
            {!! form()->close() !!}
        </x-panel>
    </div>
</div>
<div class="ui grid equal width">
    <div class="column">
        <x-panel title="Horizontal Form">
            {!! form()->get()->horizontal() !!}
            <h3 class="ui horizontal divider section">Basic Info</h3>
            {!! form()->text('nama')->label('Nama') !!}
            {!! form()->dropdown('lokasi', ['Indonesia', 'Malaysia'])->label('Lokasi') !!}
            <h3 class="ui horizontal divider section">Localization</h3>
            {!! form()->dropdown('language', ['Indonesia', 'Malaysia'])->label('Language') !!}
            {!! form()->dropdown('timezone', ['Indonesia', 'Malaysia'])->label('Timezone') !!}
            {!! form()->action(form()->submit('Simpan')) !!}
            {!! form()->close() !!}
        </x-panel>
    </div>
    <div class="column">
        <x-panel title="Vertical Form">
            {!! form()->get() !!}
            <h3 class="ui horizontal divider section">Basic Info</h3>
            {!! form()->text('nama')->label('Nama') !!}
            {!! form()->dropdown('lokasi', ['Indonesia', 'Malaysia'])->label('Lokasi') !!}
            <h3 class="ui horizontal divider section">Localization</h3>
            {!! form()->dropdown('language', ['Indonesia', 'Malaysia'])->label('Language') !!}
            {!! form()->dropdown('timezone', ['Indonesia', 'Malaysia'])->label('Timezone') !!}
            {!! form()->submit('Simpan') !!}
            {!! form()->action(form()->close()) !!}
        </x-panel>
    </div>
</div>
