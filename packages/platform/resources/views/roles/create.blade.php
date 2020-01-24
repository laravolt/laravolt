@extends(config('laravolt.epicentrum.view.layout'))

@section('page.title', __('laravolt::label.roles'))

@push('page.actions')
    <a href="{{ route('epicentrum::roles.index') }}" class="ui button">
        <i class="icon arrow up"></i> Kembali ke Index
    </a>
@endpush

@section('content')
    @component('laravolt::components.panel', ['title' => __('laravolt::label.add_role')])
        {!! SemanticForm::open()->post()->action(route('epicentrum::roles.store')) !!}
        {!! SemanticForm::text('name', old('name'))->label(trans('laravolt::roles.name'))->required() !!}

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

        <button class="ui button primary" type="submit" name="submit" value="1">@lang('laravolt::action.save')</button>
        <a href="{{ route('epicentrum::roles.index') }}" class="ui button">@lang('laravolt::action.cancel')</a>
        {!! SemanticForm::close() !!}
    @endcomponent
@endsection
