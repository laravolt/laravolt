<div class="fixed inset-0 z-50 flex items-start justify-center p-4" x-show="activeModal == '{{ $this->key }}'" x-ref="{{ $this->key }}">
    <div class="fixed inset-0 bg-gray-900/60" @click="close()"></div>
    <div class="relative z-10 mt-16 w-full max-w-lg rounded-xl bg-white shadow-lg">
        <button type="button" class="absolute right-3 top-3 rounded-md p-1 text-gray-500 hover:bg-gray-100" @click="close()" aria-label="Close">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        {{ $slot }}
    </div>
</div>
