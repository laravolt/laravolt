<x-laravolt::layout.app :title="__('laravolt::label.roles')">

    <x-slot name="actions">
        <x-laravolt::backlink url="{{ route('epicentrum::roles.index') }}"></x-laravolt::backlink>
    </x-slot>

    <x-laravolt::panel title="{{ __('laravolt::label.add_role') }}" icon="user-astronaut">
        {!! Form::open()->post()->action(route('epicentrum::roles.store')) !!}
        {!! Form::text('name', old('name'))->label(trans('laravolt::roles.name'))->required() !!}

        <table class="ui table">
            <thead>
            <tr>
                <th>
                    <div class="ui checkbox" data-toggle="checkall"
                         data-selector=".checkbox[data-type='check-all-child']">
                        <input type="checkbox">
                        <label><strong>@lang('laravolt::label.permissions')</strong></label>
                    </div>
                </th>
                <th>@lang('laravolt::permissions.description')</th>
            </tr>
            </thead>
            <tbody>
            @foreach($permissions as $permission)
                <tr>
                    <td style="width: 300px">
                        <div class="ui checkbox" data-type="check-all-child">
                            <input type="checkbox" name="permissions[]"
                                   value="{{ $permission->id }}" {{ (false)?'checked=checked':'' }}>
                            <label>{{ $permission->name }}</label>
                        </div>
                    </td>
                    <td>{{ $permission->description }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="ui divider hidden"></div>

        <div class="field action">
            <x-laravolt::button>@lang('laravolt::action.save')</x-laravolt::button>
            <x-laravolt::link-button
                    url="{{ route('epicentrum::roles.index') }}">@lang('laravolt::action.cancel')
            </x-laravolt::link-button>
        </div>

        {!! Form::close() !!}
    </x-laravolt::panel>

</x-laravolt::layout.app>
