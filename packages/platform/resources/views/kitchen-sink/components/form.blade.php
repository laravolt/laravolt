<div class="ui grid equal width">
    <div class="column">
        <x-panel title="Horizontal Form">
            {!! form()->get()->horizontal() !!}
            {!! form()->text('nama')->label('Nama') !!}
            {!! form()->dropdown('lokasi', ['Indonesia', 'Malaysia'])->label('Lokasi') !!}
            {!! form()->action(form()->submit('Simpan')) !!}
            {!! form()->close() !!}
        </x-panel>
    </div>
    <div class="column">
        <x-panel title="Vertical Form">
            {!! form()->get() !!}
            {!! form()->text('nama')->label('Nama') !!}
            {!! form()->dropdown('lokasi', ['Indonesia', 'Malaysia'])->label('Lokasi') !!}
            {!! form()->submit('Simpan') !!}
            {!! form()->action(form()->close()) !!}
        </x-panel>
    </div>
</div>
