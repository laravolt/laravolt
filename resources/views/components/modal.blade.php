<div class="fixed inset-0 z-50 flex items-start justify-center p-4" x-show="activeModal == '{{ $this->key }}'" x-ref="{{ $this->key }}">
    <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50" @click="close()"></div>
    <div class="relative w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 dark:bg-neutral-800 dark:border-neutral-700">
        <button type="button" class="absolute top-3 end-3 text-gray-400 hover:text-gray-600 dark:text-neutral-400 dark:hover:text-neutral-300" @click="close()">
            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        {{ $slot }}
    </div>
</div>
