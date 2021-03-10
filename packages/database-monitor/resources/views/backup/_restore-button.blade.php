<x-laravolt::button class="secondary" button-restore-guideline>
    <i class="icon history"></i> Restore
</x-laravolt::button>

<div class="ui modal" modal-restore-guideline>
    <i class="close icon"></i>
    <div class="header">Petunjuk Restore Database & File</div>
    <div class="content">
        <div class="ui container text">
            <ol>
                <li>SSH ke server, masuk ke folder <code>{{ base_path() }}</code></li>
                <li>Matikan aplikasi, jalankan perintah <code>php artisan down</code></li>
                <li>Buat sebuah database baru dengan nama <code>[database_baru]</code></li>
                <li>Jalankan perintah <code>php artisan backup:restore --database=[database_baru]</code>. Ikuti petunjuk yang ditampilkan.</li>
                <li>Buka file <code>.env</code> dengan menjalankan <code>vim .env</code>, ubah DB_DATABASE=[database_baru]</li>
                <li>Jalankan perintah <code>php artisan config:cache</code></li>
                <li>Nyalakan kembali aplikasi dengan perintah <code>php artisan up</code></li>
            </ol>
        </div>
    </div>
    <div class="actions">
        <div class="ui deny button">
            Tutup
        </div>
    </div>
</div>

@push('script')
    <script>
        $(function(){
          $('[modal-restore-guideline]')
            .modal('attach events', '[button-restore-guideline]', 'show')
          ;
        });
    </script>
@endpush
