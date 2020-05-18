@section('page.back')
    <div class="item p-r-0">
        <a href="{{ $url }}" class="ui button basic b-0 p-r-0">
            <i class="icon long alternate left arrow"></i>
            {{ $label ?? 'Kembali' }}
        </a>
    </div>
@endsection
