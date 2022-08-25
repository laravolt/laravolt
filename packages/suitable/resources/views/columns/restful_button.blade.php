@if($actions->isNotEmpty())
    @if($actions->count() > 1)
        <div class="x-restful-buttons ui buttons">
    @endif
            @if($actions->has('show'))
                <x-volt-link-button
                        url="{{ $actions->get('show') }}"
                        up-layer="new"
                        up-mode="modal"
                        icon="eye"
                        class="icon secondary">
                </x-volt-link-button>
            @endif

            @if($actions->has('edit'))
                <x-volt-link-button
                        url="{{ $actions->get('edit') }}"
                        up-layer="new"
                        up-mode="modal"
                        icon="pencil"
                        class="icon secondary">
                </x-volt-link-button>
            @endif

            @if($actions->has('destroy'))
                <x-volt-button form="{{ $key }}" icon="times" class="icon secondary" type="submit"></x-volt-button>
            @endif
    @if($actions->count() > 1)
        </div>
    @endif

    @if($actions->has('destroy'))
        <form id="{{ $key }}" role="form" action="{{ $actions->get('destroy') }}" method="POST" onsubmit="return confirm(`{{ $deleteConfirmation }}`)">
            <input type="hidden" name="_method" value="DELETE">
            {{ csrf_field() }}
        </form>
    @endif

@endif
