{!! form()->text('lookup_key')->label('Key')->required() !!}
{!! form()->text('lookup_value')->label('Value')->required() !!}

@if($config['parent'] ?? false)
    @php($parentLabel = config(sprintf('laravolt.lookup.collections.%s.label', $config['parent'])))
    {!! form()->dropdown('parent_key', \Laravolt\Lookup\Models\Lookup::toDropdown($config['parent']))->label($parentLabel)->required() !!}
@endif

@if($config['description'] ?? false)
    {!! form()->textarea('description')->label('Deskripsi') !!}
@endif

{!! form()->action([
    form()->submit(__('Save')),
    form()->link(__('Cancel'), route('lookup::lookup.index', $collection))
]) !!}
