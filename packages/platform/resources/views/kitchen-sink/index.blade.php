@extends('laravolt::layouts.app')

@section('content')
    <x-titlebar title="Kitchen Sink"></x-titlebar>

    @include('laravolt::kitchen-sink.components.panel')
    <div class="ui divider hidden"></div>
    @include('laravolt::kitchen-sink.components.card')
    <div class="ui divider hidden"></div>
    @include('laravolt::kitchen-sink.components.tab')
    <div class="ui divider hidden"></div>
    @include('laravolt::kitchen-sink.components.form')
    <div class="ui divider hidden"></div>
    @include('laravolt::kitchen-sink.components.definition')

    <div class="ui divider hidden"></div>

    @include('laravolt::kitchen-sink.components.table')
    <div class="ui divider hidden"></div>
    @include('laravolt::kitchen-sink.components.typography')
    <div class="ui divider hidden"></div>
    @include('laravolt::kitchen-sink.components.button')
    <div class="ui divider hidden"></div>
    @include('laravolt::kitchen-sink.components.label')

@endsection
