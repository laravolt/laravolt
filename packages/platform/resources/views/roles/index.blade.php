@extends(config('laravolt.epicentrum.view.layout'))

@section('content')

    <x-laravolt::titlebar title="{{ __('laravolt::label.roles') }}">
        <div class="item">
            <x-laravolt::link url="{{ route('epicentrum::roles.create') }}" icon="plus" label="{{ __('laravolt::action.add') }}"></x-laravolt::link>
        </div>
    </x-laravolt::titlebar>

    <div class="ui grid">
        <div class="column sixteen wide">
            <div class="ui cards three doubling">
                @foreach($roles as $role)
                    <a href="{{ route('epicentrum::roles.edit', $role['id']) }}" class="ui card">
                        <div class="content">
                            <h3 class="header link">{{ $role['name'] }}</h3>
                        </div>
                        <div class="extra content">
                            <i class="icon users"></i>{{ $role->users_count }}
                            <span class="right floated"><i class="icon options"></i> {{ $role->permissions_count }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

@endsection
