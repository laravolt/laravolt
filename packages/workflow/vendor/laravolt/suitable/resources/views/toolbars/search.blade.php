<form method="GET" action="{{ url()->current() }}">
    <div class="ui action input">
        @foreach(collect(request()->query())->except('page', $name) as $queryString => $value)
            @if(is_string($value))
            <input type="hidden" name="{{ $queryString }}" value="{{ $value }}">
            @endif
        @endforeach
        <input class="prompt" name="{{ $name }}" value="{{ request($name) }}" type="text"
               placeholder="@lang('suitable::suitable.search_placeholder')">
        <button class="ui basic button icon"><i class="search link icon"></i></button>
    </div>
</form>
