{{-- Phase 1: Form Enhancement Components --}}
<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Advanced Select</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Searchable, multi-select, and taggable dropdowns powered by <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded">hs-select</code>.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700" style="position:relative; z-index:1">
    <div class="bg-gray-100 border-b rounded-t-xl py-3 px-4 md:py-4 md:px-5 dark:bg-neutral-800 dark:border-neutral-700">
        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-500">Single select, multi-select, and grouped options</p>
    </div>
    <div class="p-4 md:p-5 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-white">Single Select</label>
                <x-volt-advanced-select
                    name="country"
                    placeholder="Choose a country..."
                    :options="['id' => 'Indonesia', 'my' => 'Malaysia', 'sg' => 'Singapore', 'th' => 'Thailand', 'ph' => 'Philippines', 'vn' => 'Vietnam']"
                />
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-white">With Groups</label>
                <x-volt-advanced-select
                    name="city"
                    placeholder="Choose a city..."
                    :options="['Indonesia' => ['jkt' => 'Jakarta', 'sby' => 'Surabaya', 'bdg' => 'Bandung'], 'Malaysia' => ['kl' => 'Kuala Lumpur', 'pg' => 'Penang']]"
                />
            </div>
        </div>
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">ComboBox</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Autocomplete search with <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded">hs-combo-box</code> for lookup-style inputs.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700" style="position:relative; z-index:1">
    <div class="bg-gray-100 border-b rounded-t-xl py-3 px-4 md:py-4 md:px-5 dark:bg-neutral-800 dark:border-neutral-700">
        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-500">Type-ahead search for relational lookups</p>
    </div>
    <div class="p-4 md:p-5">
        <div class="max-w-sm">
            <label class="block text-sm font-medium mb-2 dark:text-white">Search User</label>
            <x-volt-combobox
                name="user_id"
                placeholder="Type to search user..."
                :options="['1' => 'Ahmad Fauzi', '2' => 'Budi Santoso', '3' => 'Citra Dewi', '4' => 'Diana Putri', '5' => 'Eko Prabowo']"
            />
        </div>
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">SearchBox</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Global search input with icon and keyboard shortcut support.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
    <div class="bg-gray-100 border-b rounded-t-xl py-3 px-4 md:py-4 md:px-5 dark:bg-neutral-800 dark:border-neutral-700">
        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-500">Default, with shortcut hint, and different sizes</p>
    </div>
    <div class="p-4 md:p-5 space-y-4">
        <div class="max-w-md">
            <x-volt-searchbox placeholder="Search data master..." shortcut-key="/" />
        </div>
        <div class="flex gap-4">
            <x-volt-searchbox placeholder="Small" size="sm" class="max-w-xs" />
            <x-volt-searchbox placeholder="Large" size="lg" class="max-w-xs" />
        </div>
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Input Number</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Numeric stepper with +/− buttons powered by <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded">hs-input-number</code>.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
    <div class="bg-gray-100 border-b rounded-t-xl py-3 px-4 md:py-4 md:px-5 dark:bg-neutral-800 dark:border-neutral-700">
        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-500">Basic, with prefix/suffix, and min/max constraints</p>
    </div>
    <div class="p-4 md:p-5">
        <div class="flex flex-wrap items-end gap-6">
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-white">Quantity</label>
                <x-volt-input-number name="qty" :value="1" :min="1" :max="100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-white">Price (Rp)</label>
                <x-volt-input-number name="price" :value="50000" :step="10000" prefix="Rp" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-white">Percentage</label>
                <x-volt-input-number name="pct" :value="75" :min="0" :max="100" suffix="%" />
            </div>
        </div>
    </div>
</div>

<div class="h-8"></div>

<h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Datepicker</h2>
<p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">Native calendar picker powered by <code class="text-xs bg-gray-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded">hs-datepicker</code>.</p>

<div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
    <div class="bg-gray-100 border-b rounded-t-xl py-3 px-4 md:py-4 md:px-5 dark:bg-neutral-800 dark:border-neutral-700">
        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-500">Single date and date range variants</p>
    </div>
    <div class="p-4 md:p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-white">Tanggal Mulai</label>
                <x-volt-datepicker name="start_date" placeholder="Pilih tanggal" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-white">Rentang Tanggal</label>
                <x-volt-datepicker name="date_range" placeholder="Pilih rentang" :range="true" />
            </div>
        </div>
    </div>
</div>
