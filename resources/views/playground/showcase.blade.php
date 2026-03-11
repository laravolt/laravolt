@push('style')
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@endpush
@component('laravolt::layout.public', ['title' => 'Preline UI Showcase'])
    <div class="mb-4">
        <p class="text-sm text-gray-500 dark:text-neutral-400">
            Powered by <span class="font-semibold text-blue-600 dark:text-blue-400">Preline UI v4.1.2</span> · Tailwind CSS v4 · Interactive Component Gallery
        </p>
    </div>

    @include('laravolt::playground.components.stats')
    <div class="h-8"></div>
    @include('laravolt::playground.components.accordion')
    <div class="h-8"></div>
    @include('laravolt::playground.components.modal')
    <div class="h-8"></div>
    @include('laravolt::playground.components.tab')
    <div class="h-8"></div>
    @include('laravolt::playground.components.dropdown')
    <div class="h-8"></div>
    @include('laravolt::playground.components.tooltip')
    <div class="h-8"></div>
    @include('laravolt::playground.components.timeline')
    <div class="h-8"></div>
    @include('laravolt::playground.components.stepper')
    <div class="h-8"></div>
    @include('laravolt::playground.components.alert')
    <div class="h-8"></div>
    @include('laravolt::playground.components.table')
    <div class="h-8"></div>
    @include('laravolt::playground.components.card')
    <div class="h-8"></div>
    @include('laravolt::playground.components.definition')
    <div class="h-8"></div>
    @include('laravolt::playground.components.typography')
    <div class="h-8"></div>
    @include('laravolt::playground.components.button')
    <div class="h-8"></div>
    @include('laravolt::playground.components.label')

    {{-- New Components: Phase 1 – Form Enhancement --}}
    <div class="h-12"></div>
    <div class="border-t border-gray-200 dark:border-neutral-700 pt-8 mb-8">
        <span class="inline-flex items-center gap-x-1.5 py-1 px-3 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-500">Phase 1</span>
        <h1 class="text-xl font-bold text-gray-800 dark:text-white mt-2">Form Enhancement</h1>
        <p class="text-sm text-gray-500 dark:text-neutral-400">Advanced Select, ComboBox, SearchBox, Input Number, Datepicker</p>
    </div>
    @include('laravolt::playground.components.phase1-forms')

    {{-- Phase 2 – Data Presentation --}}
    <div class="h-12"></div>
    <div class="border-t border-gray-200 dark:border-neutral-700 pt-8 mb-8">
        <span class="inline-flex items-center gap-x-1.5 py-1 px-3 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-800/30 dark:text-teal-500">Phase 2</span>
        <h1 class="text-xl font-bold text-gray-800 dark:text-white mt-2">Data Presentation</h1>
        <p class="text-sm text-gray-500 dark:text-neutral-400">Charts, Datatables, Tree View</p>
    </div>
    @include('laravolt::playground.components.phase2-data')

    {{-- Phase 3 – Content & Upload --}}
    <div class="h-12"></div>
    <div class="border-t border-gray-200 dark:border-neutral-700 pt-8 mb-8">
        <span class="inline-flex items-center gap-x-1.5 py-1 px-3 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-500">Phase 3</span>
        <h1 class="text-xl font-bold text-gray-800 dark:text-white mt-2">Content & Upload</h1>
        <p class="text-sm text-gray-500 dark:text-neutral-400">WYSIWYG Editor, File Upload</p>
    </div>
    @include('laravolt::playground.components.phase3-content')

    {{-- Phase 4 – UX Polish --}}
    <div class="h-12"></div>
    <div class="border-t border-gray-200 dark:border-neutral-700 pt-8 mb-8">
        <span class="inline-flex items-center gap-x-1.5 py-1 px-3 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-800/30 dark:text-amber-500">Phase 4</span>
        <h1 class="text-xl font-bold text-gray-800 dark:text-white mt-2">UX Polish</h1>
        <p class="text-sm text-gray-500 dark:text-neutral-400">Avatar Group, Button Group, Input Group, Range Slider, Legend Indicator, Context Menu, Strong Password, Drag & Drop</p>
    </div>
    @include('laravolt::playground.components.phase4-ux')
@endcomponent
