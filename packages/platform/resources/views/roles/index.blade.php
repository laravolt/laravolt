@extends(config('laravolt.epicentrum.view.layout'))

@section('content')

    <div class="ui grid two column">
        <div class="column"><h2 class="ui header">@lang('laravolt::label.roles')</h2></div>
        <div class="column right aligned">
            <a href="{{ route('epicentrum::roles.create') }}" class="ui button primary"><i class="icon plus"></i> @lang('laravolt::action.add')
            </a></div>
    </div>
    <div class="ui grid">
        <div class="column sixteen wide">
            <div class="ui cards three doubling">
                @foreach($roles as $role)
                    <a href="{{ route('epicentrum::roles.edit', $role['id']) }}" class="ui card">
                        <div class="content">
                            <h3 class="header link">{{ $role['name'] }}</h3>
                        </div>
                        <div class="extra content">
                            <i class="icon users"></i>{{ $role->users->count() }}
                            <span class="right floated"><i class="icon options"></i> {{ $role->permissions()->count() }}</span>
                        </div>
                        {{--<div class="extra content">--}}
                        {{--<a href="{{ route('epicentrum::roles.edit', $role['id']) }}" class="ui button fluid"><i class="icon setting"></i> @lang('laravolt::action.manage')</a>--}}
                        {{--</div>--}}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

@endsection
