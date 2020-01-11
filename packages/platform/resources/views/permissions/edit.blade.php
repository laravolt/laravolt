@extends('ui::layouts.app')
@section('content')
    <h2 class="ui header">@lang('laravolt::label.permissions')</h2>

    {!! form()->open(route('epicentrum::permissions.update'))->put() !!}

    {!! Suitable::source($permissions)->columns([
        \Laravolt\Suitable\Columns\Numbering::make('No')->setHeaderAttributes(['width' => '50px']),
        \Laravolt\Suitable\Columns\Text::make('name', __('laravolt::permissions.name'))
            ->setHeaderAttributes(['width' => '250px']),
        \Laravolt\Suitable\Columns\Raw::make(function($item) {
            return SemanticForm::text('permission['.$item->getKey().']')->value($item->description);
        }, __('laravolt::permissions.description'))
    ])->render() !!}

    <div class="ui divider hidden"></div>

    {!! form()->submit('Save') !!}
    {!! form()->close() !!}
@endsection
