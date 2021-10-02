<form method="GET" action="" class="ui form" wire:submit.prevent="render">
    <div class="ui left icon input">
        <i class="search icon" aria-hidden="true"></i>
        <input
            type="text"
            class="prompt"
            name="{{ $searchName }}"
            value="{{ request($searchName) }}"
            wire:model.debounce.{{ $searchDebounce }}ms="search"
        >
    </div>
</form>
