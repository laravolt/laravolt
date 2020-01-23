@extends('laravolt::users.edit', ['tab' => 'password'])

@section('content-user-edit')

    <h4>@lang('laravolt::label.reset_password_manual')</h4>
    <p>@lang('laravolt::message.reset_password_manual_intro')</p>
    <form action="{{ route('epicentrum::password.reset', [$user['id']]) }}" method="POST">
    {{ csrf_field() }}
    <button type="submit" class="ui button" href="">@lang('laravolt::action.send_reset_password_link')</button>
    </form>

    <div class="ui divider"></div>

    <h4>@lang('laravolt::label.reset_password_automatic')</h4>
    <p>@lang('laravolt::message.reset_password_automatic_intro')</p>
    {!! SemanticForm::open()->post()->action(route('epicentrum::password.generate', $user['id'])) !!}
    {{ csrf_field() }}
    <div class="field">
        <div class="ui checkbox">
            <input type="checkbox" name="must_change_password" {{ request()->old('must_change_password')?'checked':'' }}>
            <label>@lang('laravolt::users.change_password_on_first_login')</label>
        </div>
    </div>
    <button type="submit" class="ui button" href="">@lang('laravolt::action.send_new_password')</button>
    {!! SemanticForm::close() !!}
@endsection
