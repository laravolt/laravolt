{{-- Phase 3: Content & Upload Components --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">WYSIWYG Editor</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Rich text editor for documents, announcements, and long-form content.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <x-volt-editor
        name="content"
        placeholder="Tulis pengumuman, surat, atau deskripsi di sini..."
        :min-height="180"
    />
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">File Upload</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Drag-and-drop file uploader with preview and constraints.</p>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Drag & Drop Upload</h3>
        <x-volt-file-upload
            name="documents"
            :multiple="true"
            accept=".pdf,.doc,.docx,.xlsx"
            :max-size="10"
            :max-files="5"
        />
    </div>
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Inline Upload</h3>
        <x-volt-file-upload
            name="photo"
            accept="image/*"
            :drag-drop="false"
        />
        <div class="mt-4">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Upload Progress Demo</h3>
            <div class="space-y-2">
                <x-volt-file-upload-progress file-name="laporan_keuangan_2024.pdf" :progress="100" file-size="2.4 MB" status="complete" />
                <x-volt-file-upload-progress file-name="surat_keputusan.docx" :progress="67" file-size="1.1 MB" status="uploading" />
                <x-volt-file-upload-progress file-name="data_pegawai.xlsx" :progress="30" file-size="4.8 MB" status="error" />
            </div>
        </div>
    </div>
</div>
