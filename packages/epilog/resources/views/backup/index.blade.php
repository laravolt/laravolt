@extends(config('laravolt.cockpit.view.layout'))

@section('content')

    <h2 class="ui header">Application Backup</h2>

    <div class="ui divider hidden"></div>

    {!! app('laravolt.folder')->openDisk('local')->render() !!}
@endsection
