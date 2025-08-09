@extends('epicentrum::edit', ['tab' => 'role'])

@section('content-user-edit')
    {!! Form::open()->put()->action(route('epicentrum::role.update', $user['id'])) !!}

    <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700">Role</label>
        @foreach($roles as $role)
        <div class="mt-2">
            <label class="inline-flex items-center gap-x-2">
                <input class="rounded border-gray-300 text-teal-600 focus:ring-teal-500" type="{{ $multipleRole?'checkbox':'radio' }}" name="roles[]" value="{{ $role->id }}" {{ ($user->hasRole($role))?'checked=checked':'' }}>
                <span>{{ $role->name }}</span>
            </label>
        </div>
        @endforeach
    </div>

    <div class="my-4 border-t border-gray-200"></div>
    <button class="inline-flex items-center gap-x-2 rounded-md bg-teal-600 px-3 py-2 text-sm font-semibold text-white hover:bg-teal-700" type="submit" name="submit" value="1">@lang('laravolt::action.save')</button>
    <a href="{{ route('epicentrum::users.index') }}" class="inline-flex items-center gap-x-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">@lang('laravolt::action.cancel')</a>
    {!! Form::close() !!}
@endsection
