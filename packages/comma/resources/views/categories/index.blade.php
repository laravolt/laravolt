@extends(config('laravolt.comma.view.layout'))

@section('content')

    <div class="ui grid two column">
        <div class="column"><h2 class="ui header">@lang('comma::category.header.index')</h2></div>
        <div class="column right aligned"><a href="{{ route('comma::categories.create') }}" class="ui button primary"><i class="icon plus"></i> @lang('comma::category.action.create')</a></div>
    </div>

    <div class="ui divider hidden"></div>

    {!! Suitable::source($categories)
    ->columns([
        ['header' => trans('comma::category.attributes.name'), 'field' => 'name'],
        ['header' => trans('comma::category.attributes.slug'), 'field' => 'slug'],
        with(new \Laravolt\Suitable\Columns\RestfulButton('comma::categories'))->only(['edit', 'delete'])
    ])
    ->render() !!}

@endsection
