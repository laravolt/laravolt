@extends(config('laravolt.epicentrum.view.layout'))

@section('page.title', __('laravolt::label.permissions'))

@section('content')

    {!! form()->open(route('epicentrum::permissions.update'))->put() !!}

    {!! Suitable::source($permissions)->columns([
        \Laravolt\Suitable\Columns\Numbering::make('No')->setHeaderAttributes(['width' => '50px']),
        \Laravolt\Suitable\Columns\Text::make('name', __('laravolt::permissions.name'))
            ->setHeaderAttributes(['width' => '250px']),
        \Laravolt\Suitable\Columns\Raw::make(function($item) {
            return SemanticForm::text('permission['.$item->getKey().']')->value($item->description);
        }, __('laravolt::permissions.description'))
    ])->render() !!}

    <div class="p-y-1">
        {!! form()->submit(__('laravolt::action.save')) !!}
    </div>
    {!! form()->close() !!}

@endsection
