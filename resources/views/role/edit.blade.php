@extends('epicentrum::edit', ['tab' => 'role'])

@section('content-user-edit')
    {!! Form::open()->put()->action(route('epicentrum::role.update', $user['id'])) !!}

    <fieldset class="space-y-3">
        <legend class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Role</legend>
        @foreach($roles as $role)
        <label class="flex items-center gap-x-3 cursor-pointer">
            <input type="{{ $multipleRole ? 'checkbox' : 'radio' }}"
                   name="roles[]"
                   value="{{ $role->id }}"
                   {{ ($user->hasRole($role)) ? 'checked' : '' }}
                   class="shrink-0 mt-0.5 border-gray-200 {{ $multipleRole ? 'rounded' : 'rounded-full' }} text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-neutral-800">
            <span class="text-sm text-gray-700 dark:text-neutral-300">{{ $role->name }}</span>
        </label>
        @endforeach
    </fieldset>

    <div class="flex items-center gap-x-3 mt-6">
        <x-volt-button type="submit" name="submit" value="1">@lang('laravolt::action.save')</x-volt-button>
        <x-volt-link-button variant="secondary" :url="route('epicentrum::users.index')">@lang('laravolt::action.cancel')</x-volt-link-button>
    </div>
    {!! Form::close() !!}
@endsection
