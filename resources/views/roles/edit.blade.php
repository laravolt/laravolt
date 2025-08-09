<x-volt-app :title="__('laravolt::label.roles')">

    <x-slot name="actions">
        <x-volt-backlink url="{{ route('epicentrum::roles.index') }}"></x-volt-backlink>
    </x-slot>

    <x-volt-panel title="{{ __('laravolt::label.edit_role') }}" icon="user-astronaut">
        {!! Form::open()->put()->action(route('epicentrum::roles.update', $role['id'])) !!}
        <div class="mb-3">
            {!! Form::text('name', old('name', $role['name']))->label(trans('laravolt::roles.name')) !!}
        </div>

        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                    <label class="inline-flex items-center gap-x-2">
                        <input type="checkbox" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500" data-toggle="checkall" data-selector=".checkbox[data-type='check-all-child']">
                        <span>@lang('laravolt::label.permissions')</span>
                        <input type="hidden" name="permissions[]" value="0">
                    </label>
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">

            @foreach($permissions as $permission)
                <tr>
                    <td class="px-3 py-2">
                        <label class="inline-flex items-start gap-x-2">
                            <input class="checkbox rounded border-gray-300 text-teal-600 focus:ring-teal-500" data-type="check-all-child" type="checkbox" name="permissions[]"
                                   value="{{ $permission->id }}" {{ (in_array($permission->id, $assignedPermissions))?'checked=checked':'' }}>
                            <span>
                                <span class="block font-medium">{{ $permission->name }}</span>
                                <span class="block text-sm text-gray-500">{{ $permission->description ?? "No description" }}</span>
                            </span>
                        </label>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>

        <div class="flex items-center gap-x-2 mt-4">
            <x-volt-button>@lang('laravolt::action.save')</x-volt-button>
            <x-volt-link-button url="{{ route('epicentrum::roles.index') }}">
                @lang('laravolt::action.cancel')
            </x-volt-link-button>
        </div>

        {!! Form::close() !!}
    </x-volt-panel>

    <div class="my-4 border-t border-gray-200"></div>

    <x-volt-panel title="{{ __('laravolt::label.delete_role') }}" icon="exclamation-triangle" icon-class="text-red-500">
        <p>@lang('laravolt::message.delete_role_intro', ['count' => $role->users->count()])</p>

        {!! Form::open()->delete()->action(route('epicentrum::roles.destroy', $role['id'])) !!}
        <div class="flex items-center gap-x-2">
            <button class="inline-flex items-center gap-x-2 rounded-md border border-red-300 bg-white px-3 py-2 text-sm text-red-600 hover:bg-red-50" type="submit" name="submit" value="1"
                    onclick="return confirm('@lang('laravolt::message.role_deletion_confirmation')')">@lang('laravolt::action.delete')
            </button>
        </div>
        {!! Form::close() !!}
    </x-volt-panel>

</x-volt-app>
