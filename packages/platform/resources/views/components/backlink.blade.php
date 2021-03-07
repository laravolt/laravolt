@section('page.actions')
    <div class="item">
        <a href="{{ $url }}" class="ui tertiary blue button">
            <i class="icon long alternate left arrow"></i>
            {{ $label ?? 'Kembali ke index' }}
        </a>
    </div>
@endsection
