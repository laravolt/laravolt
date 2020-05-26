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
