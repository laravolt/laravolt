<x-volt-app :title="__('laravolt::label.roles')">

    <x-slot name="actions">
        <x-volt-backlink url="{{ route('epicentrum::roles.index') }}"></x-volt-backlink>
    </x-slot>

    <x-volt-panel title="{{ __('laravolt::label.add_role') }}" icon="user-astronaut">
        {!! Form::open()->post()->action(route('epicentrum::roles.store')) !!}
        {!! Form::text('name', old('name'))->label(trans('laravolt::roles.name'))->required() !!}

        <div class="overflow-x-auto mt-3">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        <label class="inline-flex items-center gap-x-2">
                            <input type="checkbox" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500" data-toggle="checkall" data-selector=".checkbox[data-type='check-all-child']">
                            <span><strong>@lang('laravolt::label.permissions')</strong></span>
                        </label>
                    </th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">@lang('laravolt::permissions.description')</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($permissions as $permission)
                    <tr>
                        <td class="px-3 py-2" style="width: 300px">
                            <label class="inline-flex items-center gap-x-2">
                                <input class="checkbox rounded border-gray-300 text-teal-600 focus:ring-teal-500" data-type="check-all-child" type="checkbox" name="permissions[]"
                                       value="{{ $permission->id }}" {{ (false)?'checked=checked':'' }}>
                                <span>{{ $permission->name }}</span>
                            </label>
                        </td>
                        <td class="px-3 py-2">{{ $permission->description }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="my-4 border-t border-gray-200"></div>

        <div class="flex items-center gap-x-2">
            <x-volt-button>@lang('laravolt::action.save')</x-volt-button>
            <x-volt-link-button
                    url="{{ route('epicentrum::roles.index') }}">@lang('laravolt::action.cancel')
            </x-volt-link-button>
        </div>

        {!! Form::close() !!}
    </x-volt-panel>

</x-volt-app>
