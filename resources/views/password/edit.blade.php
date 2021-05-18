@extends('laravolt::users.edit', ['tab' => 'password'])

@section('content-user-edit')

    <h4>@lang('laravolt::label.reset_password_manual')</h4>
    <p>@lang('laravolt::message.reset_password_manual_intro')</p>
    <form action="{{ route('epicentrum::password.reset', [$user['id']]) }}" method="POST">
    {{ csrf_field() }}
    <x-volt-button>@lang('laravolt::action.send_reset_password_link')</x-volt-button>
    </form>

    <div class="ui divider"></div>

    <h4>@lang('laravolt::label.reset_password_automatic')</h4>
    <p>@lang('laravolt::message.reset_password_automatic_intro')</p>
    {!! Form::open()->post()->action(route('epicentrum::password.generate', $user['id'])) !!}
    {{ csrf_field() }}
    <div class="field">
        <div class="ui checkbox">
            <input type="checkbox" name="must_change_password" {{ request()->old('must_change_password')?'checked':'' }}>
            <label>@lang('laravolt::users.change_password_on_first_login')</label>
        </div>
    </div>
    <x-volt-button>@lang('laravolt::action.send_new_password')</x-volt-button>
    {!! Form::close() !!}
@endsection
