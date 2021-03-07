@extends('laravolt::layouts.app')

@section('content')
    <x-laravolt::titlebar title="Kitchen Sink"></x-laravolt::titlebar>

    @include('laravolt::playground.components.panel')
    <div class="ui divider hidden"></div>
    @include('laravolt::playground.components.card')
    <div class="ui divider hidden"></div>
    @include('laravolt::playground.components.modal')
    <div class="ui divider hidden"></div>
    @include('laravolt::playground.components.tab')
    <div class="ui divider hidden"></div>
    @include('laravolt::playground.components.form')
    <div class="ui divider hidden"></div>
    @include('laravolt::playground.components.definition')

    <div class="ui divider hidden"></div>

    @include('laravolt::playground.components.table')
    <div class="ui divider hidden"></div>
    @include('laravolt::playground.components.typography')
    <div class="ui divider hidden"></div>
    @include('laravolt::playground.components.button')
    <div class="ui divider hidden"></div>
    @include('laravolt::playground.components.label')
    <div class="ui divider hidden"></div>
    @include('laravolt::playground.components.media')

@endsection
