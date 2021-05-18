@extends('laravolt::layouts.app')

@section('content')

    {!! form()->open(route('platform::dump')) !!}
    {!! form()->dropdown('foo', ['a' => 'foobar', 'b' => 'foobaz'])->multiple()->addClass('tag') !!}
    {!! form()->submit('Dump') !!}
    {!! form()->text('bar') !!}
    {!! form()->close() !!}
    <x-volt-titlebar title="Article Editor"></x-volt-titlebar>



@endsection

@push('script')
    <script>

    </script>
@endpush
