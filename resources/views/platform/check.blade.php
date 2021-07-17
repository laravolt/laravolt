<x-volt-app title="Platform Check">
    <div class="ui container">
        <x-volt-panel title="Form Submission Check">
            {!! form()->post(route('platform::dump'))->multipart() !!}
            {!! form()->uploader('file')->ajax(false)->label('Upload File')->hint('Max file upload detected from php.ini: ' . \Laravolt\platform_max_file_upload(true)) !!}
            {!! form()->uploader('files')->ajax(false)->limit(9)->label('Multiple Upload File')->hint('Max file upload detected from php.ini: ' . \Laravolt\platform_max_file_upload(true)) !!}
            {!! form()->redactor('rich_content')->label('Rich Content')->hint('Beberapa web application firewall melarang pengiriman tag HTML atau script JS via form.') !!}
            {!! form()->textarea('long_content')->label('Long Content')->hint('Beberapa web application firewall membatasi jumlah byte yang bisa dikirim ketika submit form.') !!}
            {!! form()->submit('Test Submit Form') !!}
            {!! form()->close() !!}
        </x-volt-panel>
    </div>
</x-volt-app>
