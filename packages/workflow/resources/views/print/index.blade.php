@extends('laravolt::layouts.app-surat', ['url' => asset($path), 'extension' => $extension])

@section('content')

    @if($extension == 'pdf')
        <iframe
            src="{{ asset($path) }}"
            width="100%"
            frameborder="0"
            style="height: calc(100vh - 70px)"
        ></iframe>
    @else
        <iframe
            src="https://view.officeapps.live.com/op/embed.aspx?src={{ asset($path) }}"
            width="100%"
            frameborder="0"
            style="height: calc(100vh - 70px)"
        ></iframe>
    @endif
@endsection
