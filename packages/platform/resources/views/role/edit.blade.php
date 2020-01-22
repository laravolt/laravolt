@extends('epicentrum::edit', ['tab' => 'role'])

@section('content-user-edit')
    {!! SemanticForm::open()->put()->action(route('epicentrum::role.update', $user['id'])) !!}

    <div class="grouped fields">
        <label>Role</label>
        @foreach($roles as $role)
        <div class="field">
            <div class="ui checkbox {{ $multipleRole?'':'radio' }}">
                <input type="{{ $multipleRole?'checkbox':'radio' }}" name="roles[]" value="{{ $role->id }}" {{ ($user->hasRole($role))?'checked=checked':'' }}>
                <label>{{ $role->name }}</label>
            </div>
        </div>
        @endforeach
    </div>

    <div class="ui divider hidden"></div>
    <button class="ui button primary" type="submit" name="submit" value="1">@lang('laravolt::action.save')</button>
    <a href="{{ route('epicentrum::users.index') }}" class="ui button">@lang('laravolt::action.cancel')</a>
    {!! SemanticForm::close() !!}
@endsection
