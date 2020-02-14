@extends('ui::layouts.app')

@section('content')
    @foreach($processDefinitionKeys as $key)
        @component('laravolt::components.panel', ['title' => $key])
            @component('workflow::components.diagram-with-counter', ['key' => $key])
            @endcomponent
        @endcomponent
    @endforeach
@endsection
