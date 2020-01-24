@extends(config('laravolt.epicentrum.view.layout'))

@section('page.title', __('laravolt::label.users'))

@push('page.actions')
    <a href="{{ route('epicentrum::users.index') }}" class="ui button">
        <i class="icon arrow up"></i> Kembali ke Index
    </a>
@endpush

@section('content')
    @component('laravolt::components.panel', ['title' => __('laravolt::menu.add_user')])
        {!! form()->open()->post()->action(route('epicentrum::users.store')) !!}
        {!! form()->text('name')->label(trans('laravolt::users.name'))->required() !!}
        {!! form()->text('email')->label(trans('laravolt::users.email'))->required() !!}
        {!! form()->input('password')->appendButton(trans('laravolt::action.generate_password'), 'randomize')->label(trans('laravolt::users.password'))->required() !!}

        @if($multipleRole)
            {!! form()->checkboxGroup('roles', $roles)->label(trans('laravolt::users.roles')) !!}
        @else
            {!! form()->radioGroup('roles', $roles)->label(trans('laravolt::users.roles')) !!}
        @endif

        {!! form()->select('status', $statuses)->label(__('laravolt::users.status')) !!}
        {!! form()->select('timezone', $timezones, config('app.timezone'))->label(__('laravolt::users.timezone')) !!}

        <div class="ui divider section"></div>

        {!! form()->checkbox('send_account_information', 1)->label(__('laravolt::users.send_account_information_via_email')) !!}
        {!! form()->checkbox('must_change_password', 1)->label(__('laravolt::users.change_password_on_first_login')) !!}

        <div class="ui divider section"></div>

        {!! form()->action(form()->submit(__('laravolt::action.save')), form()->link(__('laravolt::action.back'), route('epicentrum::users.index'))) !!}
        {!! form()->close() !!}

    @endcomponent

@endsection


@push('body')
    <script>
      $(function () {
        $('.randomize').on('click', function (e) {
          $(e.currentTarget).prev().val(Math.random().toString(36).substr(2, 8));
        });
      });
    </script>
@endpush
