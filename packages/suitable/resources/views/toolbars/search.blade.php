<div class="item">
    <form method="GET" action="{{ request()->fullUrl() }}">
        <div class="ui transparent icon input">
            <input class="prompt" name="{{ $search }}" value="{{ request($search) }}" type="text" placeholder="@lang('suitable::suitable.search_placeholder')">
            <i class="search link icon"></i>
        </div>
    </form>
</div>
