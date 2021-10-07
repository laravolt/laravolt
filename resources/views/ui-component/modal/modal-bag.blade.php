<div
        class="ui page dimmer modals volt-modal-dimmer"
        :class="{ 'transition visible fade in' : loading && (activeModal === null), 'active' : show }"
        x-data="LivewireModal()"
        x-on:keydown.escape.window="close()"
>
    <div x-show="loading" class="ui elastic loader {{ config('laravolt.ui.color') }}"></div>

    @foreach($modals as $id => $modal)
        @livewire($modals[$activeModal]['name'], ['key' => $id], key($id))
    @endforeach
</div>
