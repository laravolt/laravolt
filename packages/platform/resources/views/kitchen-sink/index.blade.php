@extends('laravolt::layouts.app')

@section('content')
    <x-titlebar title="Kitchen Sink"></x-titlebar>

    @include('laravolt::kitchen-sink.components.panel')
    @include('laravolt::kitchen-sink.components.card')
    @include('laravolt::kitchen-sink.components.form')
    @include('laravolt::kitchen-sink.components.definition')

    <div class="ui divider hidden"></div>

    @include('laravolt::kitchen-sink.components.table')
    @include('laravolt::kitchen-sink.components.typography')
    @include('laravolt::kitchen-sink.components.button')
    @include('laravolt::kitchen-sink.components.label')

@endsection
