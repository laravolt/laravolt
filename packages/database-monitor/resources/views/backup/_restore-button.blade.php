<x-volt-button class="secondary" data-hs-overlay="#restore-guideline-modal">
    Restore
</x-volt-button>

<div id="restore-guideline-modal" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto">
        <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-800">
            <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                <h3 class="font-semibold text-gray-800 dark:text-white">Petunjuk Restore Database & File</h3>
                <button type="button" class="inline-flex justify-center items-center w-7 h-7 rounded-full text-sm font-semibold border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:border-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700" data-hs-overlay="#restore-guideline-modal">✕</button>
            </div>
            <div class="p-4">
                <div class="prose prose-sm max-w-none dark:prose-invert">
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
            <div class="flex items-center justify-end gap-x-2 p-4 border-t dark:border-neutral-700">
                <button type="button" class="inline-flex items-center gap-x-2 rounded-lg border border-gray-200 bg-white text-gray-800 px-3 py-2 text-sm hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700" data-hs-overlay="#restore-guideline-modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

