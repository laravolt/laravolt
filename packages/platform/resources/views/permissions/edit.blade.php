@extends(config('laravolt.epicentrum.view.layout'))

@section('content')

    <x-titlebar title="{{ __('laravolt::label.permissions') }}"></x-titlebar>

    <x-panel title="Atur Deskripsi Hak Akses">
        <div class="ui message">
            <p>Memberikan deskripsi yang jelas akan membantu admin aplikasi ketika melakukan pengaturan hak akses bagi setiap role.</p>
        </div>
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

    </x-panel>

@endsection
