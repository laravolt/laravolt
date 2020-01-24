@extends('laravolt::layouts.app')

@section('content')
    <div class="ui grid">
        <div class="sixteen wide column">

            <a href="{{ route('segment.create') }}" class="right floated column ui primary button">
                <i class="plus icon"></i>
                Tambah
            </a>
        </div>
    </div>
    {!! $table !!}
@endsection

