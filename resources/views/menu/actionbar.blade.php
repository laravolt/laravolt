<div id="actionbar" class="ui two column grid p-x-3 p-y-1 m-b-0">
    <div class="column middle aligned">
        @yield('breadcrumb')
        <h3 class="ui header m-t-xs">
            {{ $title }}
        </h3>
    </div>
    <div class="column right aligned middle aligned">
        {{ $actions ?? '' }}
    </div>
</div>
