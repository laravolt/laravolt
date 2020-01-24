@extends(config('laravolt.epicentrum.view.layout'))

@section('content')

    @component('laravolt::components.panel', ['title' => __('Edit Profil')])
        {!! form()->bind($user)->put(route('epicentrum::my.profile.update'))->horizontal() !!}

        {!! form()->text('name')->label(__('laravolt::users.name')) !!}
        {!! form()->text('email')->label(__('laravolt::users.email'))->readonly() !!}
        {!! form()->dropdown('timezone', $timezones)->label(__('laravolt::users.timezone')) !!}

        {!! form()->action(form()->submit(__('laravolt::action.save'))) !!}
        {!! form()->close() !!}
    @endcomponent
@endsection
