<form method="GET" action="{{ url()->current() }}">
    <div class="ui transparent action input">
        <input class="prompt" name="{{ $name }}" value="{{ request($name) }}" type="text"
               placeholder="@lang('suitable::suitable.search_placeholder')">
        <button class="ui submit basic button icon b-0"><i class="search link icon"></i></button>
    </div>
</form>
