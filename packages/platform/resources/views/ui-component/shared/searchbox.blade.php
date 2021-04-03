<form method="GET" action="" class="ui form" wire:submit.prevent="render">
    <div class="ui icon input small">
        <input
            type="text"
            class="prompt"
            name="{{ $name }}"
            value="{{ request($name) }}"
            wire:model.debounce.300ms="search"
        >
        <i class="search icon" aria-hidden="true"></i>
    </div>
</form>
