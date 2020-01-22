<div class="ui dropdown">
    <div class="text">{{ $options[request($name)] ?? $label }}</div>
    <i class="dropdown icon"></i>
    <div class="menu">
        @foreach($options as $key => $value)
            <a class="item" href="{{ url()->current() }}?{{ $name }}={{ $key }}">{{ $value }}</a>
        @endforeach
    </div>
</div>
