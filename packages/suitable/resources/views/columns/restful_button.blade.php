@if($actions->isNotEmpty())
    @if($actions->count() > 1)
        <div class="x-restful-buttons ui buttons">
    @endif
            @if($actions->has('view'))
                <x-laravolt::link-button url="{{ $actions->get('view') }}" icon="eye" class="mini icon secondary"></x-laravolt::link-button>
            @endif

            @if($actions->has('edit'))
                <x-laravolt::link-button url="{{ $actions->get('edit') }}" icon="pencil" class="mini icon secondary"></x-laravolt::link-button>
            @endif

            @if($actions->has('delete'))
                <x-laravolt::button form="{{ $key }}" icon="trash alternate" class="icon mini secondary" type="submit"></x-laravolt::button>
            @endif
    @if($actions->count() > 1)
        </div>
    @endif

    @if($actions->has('delete'))
        <form id="{{ $key }}" role="form" action="{{ $actions->get('delete') }}" method="POST" onsubmit="return confirm('{{ $deleteConfirmation }}')">
            <input type="hidden" name="_method" value="DELETE">
            {{ csrf_field() }}
        </form>
    @endif

@endif
