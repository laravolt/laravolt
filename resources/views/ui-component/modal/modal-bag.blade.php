<div
        class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60"
        :class="{ 'flex' : loading && (activeModal === null), 'flex' : show }"
        x-data="LivewireModal()"
        x-on:keydown.escape.window="close()"
>
    <div x-show="loading" class="h-10 w-10 animate-spin rounded-full border-4 border-teal-600 border-t-transparent"></div>

    @foreach($modals as $id => $modal)
        @livewire($modals[$activeModal]['name'], ['key' => $id], key($id))
    @endforeach
</div>
