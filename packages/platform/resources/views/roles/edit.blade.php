@extends(config('laravolt.epicentrum.view.layout'))

@section('page.title', __('laravolt::label.roles'))

@push('page.actions')
    <a href="{{ route('epicentrum::roles.index') }}" class="ui button">
        <i class="icon arrow up"></i> Kembali ke Index
    </a>
@endpush


@section('content')
    @component('laravolt::components.panel', ['title' => __('laravolt::label.edit_role')])
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

        <button class="ui button primary" type="submit" name="submit" value="1">@lang('laravolt::action.save')</button>
        <a href="{{ route('epicentrum::roles.index') }}" class="ui button">@lang('laravolt::action.cancel')</a>
        {!! SemanticForm::close() !!}
    @endcomponent

    <div class="ui divider section hidden"></div>

    <div class="ui segment very padded red">
        <h3 class="">@lang('laravolt::label.delete_role')</h3>
        <p>@lang('laravolt::message.delete_role_intro', ['count' => $role->users->count()])</p>

        {!! SemanticForm::open()->delete()->action(route('epicentrum::roles.destroy', $role['id'])) !!}
        <button class="ui button red" type="submit" name="submit" value="1"
                onclick="return confirm('@lang('laravolt::message.role_deletion_confirmation')')">@lang('laravolt::action.delete')
        </button>
        {!! SemanticForm::close() !!}
    </div>

@endsection
