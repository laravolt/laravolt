<form method="GET" action="" class="ui form" >
    <div class="ui left icon input">
        <i class="search icon" aria-hidden="true"></i>
        <input
            type="text"
            class="prompt"
            name="{{ $searchName }}"
            value="{{ request($searchName) }}"
            placeholder="{{ $this->searchPlaceholder ?? __('laravolt::action.search') }}"
            wire:model.debounce.{{ $searchDebounce }}ms="search"
        >
    </div>
</form>
