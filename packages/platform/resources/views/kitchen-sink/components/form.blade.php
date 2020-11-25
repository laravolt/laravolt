<div class="ui grid">
    <div class="column">
        <x-panel title="Form Fields">
            {!! form()->open()->multipart() !!}
            {!! form()->text('text')->label('Text') !!}
            {!! form()->email('email')->label('Email') !!}
            {!! form()->number('number')->label('Number') !!}
            {!! form()->date('date')->label('Date') !!}
            {!! form()->time('time')->label('Time') !!}
            {!! form()->rupiah('rupiah1')->label('Rupiah') !!}
            {!! form()->rupiah('rupiah2')->label('Rupiah (dengan koma)') !!}
            {!! form()->coordinate('koordinat')->label('Koordinat') !!}
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
