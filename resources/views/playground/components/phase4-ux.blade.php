{{-- Phase 4: UX Polish Components --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Avatar Group</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Stacked avatars for showing team members, reviewers, or participants.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <div class="flex flex-wrap items-center gap-8">
        <div>
            <p class="text-xs text-gray-500 dark:text-neutral-400 mb-2">Small (3 max)</p>
            <x-volt-avatar-group size="sm" :max="3" :avatars="[
                ['name' => 'Ahmad Fauzi'],
                ['name' => 'Budi Santoso'],
                ['name' => 'Citra Dewi'],
                ['name' => 'Diana Putri'],
                ['name' => 'Eko Prabowo'],
            ]" />
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-neutral-400 mb-2">Medium (default)</p>
            <x-volt-avatar-group :avatars="[
                ['name' => 'Ahmad Fauzi'],
                ['name' => 'Budi Santoso'],
                ['name' => 'Citra Dewi'],
            ]" />
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-neutral-400 mb-2">Large (5 max)</p>
            <x-volt-avatar-group size="lg" :max="5" :avatars="[
                ['name' => 'Reviewer 1'],
                ['name' => 'Reviewer 2'],
                ['name' => 'Reviewer 3'],
                ['name' => 'Reviewer 4'],
                ['name' => 'Reviewer 5'],
                ['name' => 'Reviewer 6'],
                ['name' => 'Reviewer 7'],
            ]" />
        </div>
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Button Group</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Grouped action buttons for toolbar-style controls.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <div class="flex flex-wrap gap-4">
        <x-volt-button-group>
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 -ms-px first:rounded-s-lg first:ms-0 last:rounded-e-lg text-sm font-medium border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">Approve</button>
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 -ms-px first:rounded-s-lg first:ms-0 last:rounded-e-lg text-sm font-medium border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">Revise</button>
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 -ms-px first:rounded-s-lg first:ms-0 last:rounded-e-lg text-sm font-medium border border-gray-200 bg-white text-red-600 shadow-sm hover:bg-red-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-red-400 dark:hover:bg-red-900/30">Reject</button>
        </x-volt-button-group>

        <x-volt-button-group>
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 -ms-px first:rounded-s-lg first:ms-0 last:rounded-e-lg text-sm font-medium border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                Print
            </button>
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 -ms-px first:rounded-s-lg first:ms-0 last:rounded-e-lg text-sm font-medium border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Export
            </button>
        </x-volt-button-group>
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Input Group</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Input fields with prefix/suffix text like currency symbols, units, or percentages.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium mb-2 dark:text-white">Harga</label>
            <x-volt-input-group prefix="Rp">
                <input type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-e-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="0">
            </x-volt-input-group>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2 dark:text-white">Persentase</label>
            <x-volt-input-group suffix="%">
                <input type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-s-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="0" min="0" max="100">
            </x-volt-input-group>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2 dark:text-white">Domain</label>
            <x-volt-input-group prefix="https://" suffix=".com">
                <input type="text" class="py-2 px-3 block w-full border-gray-200 shadow-sm text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="domain">
            </x-volt-input-group>
        </div>
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Range Slider</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Slider input for scoring, pricing, and percentage values.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <div class="space-y-6 max-w-lg">
        <div>
            <label class="block text-sm font-medium mb-2 dark:text-white">Skor Penilaian</label>
            <x-volt-range-slider name="score" :value="75" :min="0" :max="100" />
        </div>
        <div>
            <label class="block text-sm font-medium mb-2 dark:text-white">Budget Range</label>
            <x-volt-range-slider name="budget" :value="50" :min="0" :max="200" :step="10" />
        </div>
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Legend Indicator</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Status indicators and chart legends with colored dots.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <div class="space-y-4">
        <x-volt-legend-indicator :items="[
            ['label' => 'Approved', 'color' => 'bg-teal-500', 'value' => '40%'],
            ['label' => 'Pending', 'color' => 'bg-yellow-400', 'value' => '25%'],
            ['label' => 'In Review', 'color' => 'bg-blue-500', 'value' => '20%'],
            ['label' => 'Rejected', 'color' => 'bg-red-500', 'value' => '15%'],
        ]" />
        <x-volt-legend-indicator layout="vertical" :items="[
            ['label' => 'Online', 'color' => 'bg-green-500'],
            ['label' => 'Away', 'color' => 'bg-yellow-400'],
            ['label' => 'Offline', 'color' => 'bg-gray-400'],
        ]" />
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Context Menu</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Right-click menu for power-user actions on table rows or elements.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <x-volt-context-menu :items="[
        ['label' => 'View Detail'],
        ['label' => 'Edit'],
        ['label' => 'Duplicate'],
        ['divider' => true],
        ['label' => 'Delete', 'danger' => true],
    ]">
        <div class="p-6 border-2 border-dashed border-gray-300 rounded-lg text-center text-sm text-gray-500 dark:border-neutral-600 dark:text-neutral-400 cursor-context-menu">
            <p class="font-semibold text-gray-800 dark:text-white">Right-click here</p>
            <p class="mt-1">Klik kanan untuk melihat context menu</p>
        </div>
    </x-volt-context-menu>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Strong Password</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Password strength meter with visual indicator and requirement checklist.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <div class="max-w-sm">
        <label class="block text-sm font-medium mb-2 dark:text-white">Password Baru</label>
        <x-volt-strong-password name="password" :min-length="8" />
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Drag & Drop</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Sortable lists powered by <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded">SortableJS</code> for reordering items.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-4 md:p-5">
    <div class="max-w-md">
        <x-volt-drag-drop group="priority">
            @foreach(['Verifikasi dokumen', 'Review oleh atasan', 'Persetujuan akhir', 'Notifikasi stakeholder', 'Arsip dokumen'] as $i => $item)
                <div class="flex items-center gap-x-3 p-3 bg-white border border-gray-200 rounded-lg cursor-move dark:bg-neutral-800 dark:border-neutral-700">
                    <svg class="shrink-0 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>
                    <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $i + 1 }}. {{ $item }}</span>
                </div>
            @endforeach
        </x-volt-drag-drop>
    </div>
</div>
