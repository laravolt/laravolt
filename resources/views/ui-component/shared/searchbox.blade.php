<form method="GET" action="" class="ui form" wire:submit.prevent="render">
    <div class="ui left icon input">
        <i class="search icon" aria-hidden="true"></i>
        <input
            type="text"
            class="prompt"
            name="{{ $name }}"
            value="{{ request($name) }}"
            wire:model.debounce.300ms="search"
        >
    </div>
</form>
