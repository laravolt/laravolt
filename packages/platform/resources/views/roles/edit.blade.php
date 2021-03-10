<x-laravolt::layout.app :title="__('laravolt::label.roles')">

    <x-slot name="actions">
        <x-laravolt::backlink url="{{ route('epicentrum::roles.index') }}"></x-laravolt::backlink>
    </x-slot>

    <x-laravolt::panel title="{{ __('laravolt::label.edit_role') }}">
        {!! SemanticForm::open()->put()->action(route('epicentrum::roles.update', $role['id'])) !!}
        <div class="field required">
            {!! SemanticForm::text('name', old('name', $role['name']))->label(trans('laravolt::roles.name')) !!}
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

        <x-laravolt::button>@lang('laravolt::action.save')</x-laravolt::button>
        <x-laravolt::link-button
                url="{{ route('epicentrum::roles.index') }}">@lang('laravolt::action.cancel')</x-laravolt::link-button>

        {!! SemanticForm::close() !!}
    </x-laravolt::panel>

    <div class="ui divider hidden"></div>

    <x-laravolt::panel title="{{ __('laravolt::label.delete_role') }}" icon="warning red">
        <p>@lang('laravolt::message.delete_role_intro', ['count' => $role->users->count()])</p>

        {!! SemanticForm::open()->delete()->action(route('epicentrum::roles.destroy', $role['id'])) !!}
        <button class="ui button red" type="submit" name="submit" value="1"
                onclick="return confirm('@lang('laravolt::message.role_deletion_confirmation')')">@lang('laravolt::action.delete')
        </button>
        {!! SemanticForm::close() !!}
    </x-laravolt::panel>

</x-laravolt::layout.app>
