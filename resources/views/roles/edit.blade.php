<x-volt-app :title="__('laravolt::label.roles')">

    <x-slot name="actions">
        <x-volt-backlink url="{{ route('epicentrum::roles.index') }}"></x-volt-backlink>
    </x-slot>

    <x-volt-panel title="{{ __('laravolt::label.edit_role') }}" icon="user-astronaut">
        {!! Form::open()->put()->action(route('epicentrum::roles.update', $role['id'])) !!}
        <div class="field required">
            {!! Form::text('name', old('name', $role['name']))->label(trans('laravolt::roles.name')) !!}
        </div>

        <table class="ui table padded">
            <thead>
            <tr>
                <th>
                    <div class="ui checkbox" data-toggle="checkall"
                         data-selector=".checkbox[data-type='check-all-child']">
                        <input type="checkbox">
                        <label>@lang('laravolt::label.permissions')</label>
                        <input type="hidden" name="permissions[]" value="0">
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>

            @foreach($permissions as $permission)
                <tr>
                    <td>
                        <div class="ui checkbox" data-type="check-all-child">
                            <input type="checkbox" name="permissions[]"
                                   value="{{ $permission->id }}" {{ (in_array($permission->id, $assignedPermissions))?'checked=checked':'' }}>
                            <label>
                                <h5 class="m-y-0 m-l-1">{{ $permission->name }}</h5>
                                <p class="m-l-1">
                                    {{ $permission->description ?? "No description" }}
                                </p>
                            </label>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="actions">
            <x-volt-button>@lang('laravolt::action.save')</x-volt-button>
            <x-volt-link-button url="{{ route('epicentrum::roles.index') }}">
                @lang('laravolt::action.cancel')
            </x-volt-link-button>
        </div>

        {!! Form::close() !!}
    </x-volt-panel>

    <div class="ui divider hidden"></div>

    <x-volt-panel title="{{ __('laravolt::label.delete_role') }}" icon="exclamation-triangle" icon-class="text-red-500">
        <p>@lang('laravolt::message.delete_role_intro', ['count' => $role->users->count()])</p>

        {!! Form::open()->delete()->action(route('epicentrum::roles.destroy', $role['id'])) !!}
        <div class="actions">
            <button class="ui button basic red" type="submit" name="submit" value="1"
                    onclick="return confirm('@lang('laravolt::message.role_deletion_confirmation')')">@lang('laravolt::action.delete')
            </button>
        </div>
        {!! Form::close() !!}
    </x-volt-panel>

</x-volt-app>
