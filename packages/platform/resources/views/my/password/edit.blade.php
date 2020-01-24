@extends(config('laravolt.epicentrum.view.layout'))

@section('content')

    @component('laravolt::components.panel', ['title' => __('Edit Password')])
        {!! form()->open()->action(route('epicentrum::my.password.update'))->horizontal() !!}
        {!! form()->password('password_current')->label(__('laravolt::users.password_current')) !!}
        {!! form()->password('password')->label(__('laravolt::users.password_new')) !!}
        {!! form()->password('password_confirmation')->label(__('laravolt::users.password_new_confirmation')) !!}
        {!! form()->action(form()->submit(__('laravolt::action.save'))) !!}
        {!! form()->close() !!}
    @endcomponent
@endsection
