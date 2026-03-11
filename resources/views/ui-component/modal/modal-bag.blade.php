<div
        class="fixed inset-0 z-80 overflow-x-hidden overflow-y-auto bg-gray-900/50 dark:bg-neutral-900/80 volt-modal-dimmer"
        :class="{ 'flex items-center justify-center' : loading && (activeModal === null), '' : !show }"
        x-data="LivewireModal()"
        x-on:keydown.escape.window="close()"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
>
    <div x-show="loading" class="flex items-center justify-center">
        <div class="animate-spin inline-block size-8 border-[3px] border-current border-t-transparent text-blue-600 rounded-full dark:text-blue-500" role="status" aria-label="loading">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    @foreach($modals as $id => $modal)
        @livewire($modals[$activeModal]['name'], ['key' => $id], key($id))
    @endforeach
</div>
