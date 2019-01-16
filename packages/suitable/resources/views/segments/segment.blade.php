@foreach($left as $toolbar)
    <div class="item">{!! $toolbar->render() !!}</div>
@endforeach

<div class="menu right">
    @foreach($right as $toolbar)
        <div class="item">{!! $toolbar->render() !!}</div>
    @endforeach
</div>
