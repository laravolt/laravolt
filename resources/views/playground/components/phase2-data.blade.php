{{-- Phase 2: Data Presentation Components --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Charts</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Interactive charts powered by <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded">ApexCharts</code> — bar, line, area, pie, donut.</p>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Bar Chart — Revenue per Quarter</h3>
        <x-volt-chart
            type="bar"
            :series="[['name' => 'Revenue', 'data' => [44000, 55000, 41000, 67000]], ['name' => 'Expense', 'data' => [35000, 41000, 36000, 52000]]]"
            :categories="['Q1', 'Q2', 'Q3', 'Q4']"
            :height="280"
        />
    </div>
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Line Chart — Monthly Users</h3>
        <x-volt-chart
            type="line"
            :series="[['name' => 'Users', 'data' => [120, 190, 300, 500, 200, 340, 450, 520, 700, 610, 800, 950]]]"
            :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
            :height="280"
            :colors="['#10b981']"
        />
    </div>
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Donut Chart — Status Distribution</h3>
        <x-volt-chart
            type="donut"
            :series="[40, 25, 20, 15]"
            :categories="['Approved', 'Pending', 'In Review', 'Rejected']"
            :height="280"
            :colors="['#10b981', '#f59e0b', '#3b82f6', '#ef4444']"
        />
    </div>
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Area Chart — Performance Trend</h3>
        <x-volt-chart
            type="area"
            :series="[['name' => 'Desktop', 'data' => [31, 40, 28, 51, 42, 109, 100]], ['name' => 'Mobile', 'data' => [11, 32, 45, 32, 34, 52, 41]]]"
            :categories="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
            :height="280"
            :colors="['#8b5cf6', '#ec4899']"
        />
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Datatable</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Interactive data table with search, sorting, and pagination.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <x-volt-datatable
        :columns="[
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'role', 'label' => 'Role'],
            ['key' => 'status', 'label' => 'Status'],
        ]"
        :data="[
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@example.com', 'role' => 'Admin', 'status' => 'Active'],
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'role' => 'Editor', 'status' => 'Active'],
            ['name' => 'Citra Dewi', 'email' => 'citra@example.com', 'role' => 'Viewer', 'status' => 'Inactive'],
            ['name' => 'Diana Putri', 'email' => 'diana@example.com', 'role' => 'Editor', 'status' => 'Active'],
            ['name' => 'Eko Prabowo', 'email' => 'eko@example.com', 'role' => 'Admin', 'status' => 'Active'],
            ['name' => 'Farah Amelia', 'email' => 'farah@example.com', 'role' => 'Viewer', 'status' => 'Pending'],
        ]"
    />
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Tree View</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Hierarchical data display for org structures, categories, and menus.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Struktur Organisasi</h3>
            <x-volt-tree-view :items="[
                ['label' => 'Direktorat Utama', 'children' => [
                    ['label' => 'Divisi Keuangan', 'children' => [
                        ['label' => 'Dept. Akuntansi'],
                        ['label' => 'Dept. Treasury'],
                    ]],
                    ['label' => 'Divisi SDM', 'children' => [
                        ['label' => 'Dept. Rekrutmen'],
                        ['label' => 'Dept. Pengembangan'],
                    ]],
                ]],
                ['label' => 'Direktorat Operasional', 'children' => [
                    ['label' => 'Divisi Produksi'],
                    ['label' => 'Divisi Logistik'],
                ]],
            ]" />
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Kategori Dokumen</h3>
            <x-volt-tree-view :items="[
                ['label' => 'Surat Masuk', 'children' => [
                    ['label' => 'Surat Undangan'],
                    ['label' => 'Surat Permohonan'],
                    ['label' => 'Surat Pemberitahuan'],
                ]],
                ['label' => 'Surat Keluar', 'children' => [
                    ['label' => 'SK Keputusan'],
                    ['label' => 'Surat Edaran'],
                ]],
                ['label' => 'Laporan', 'children' => [
                    ['label' => 'Laporan Harian'],
                    ['label' => 'Laporan Bulanan'],
                    ['label' => 'Laporan Tahunan'],
                ]],
            ]" :checkbox="true" />
        </div>
    </div>
</div>
