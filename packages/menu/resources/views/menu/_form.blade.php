{!! form()->dropdown('parent_id', $parent)->label('Parent Menu')->addClass('clearable')->placeholder('Top Level Menu') !!}
{!! form()->text('label')->label('Label')->required() !!}
{!! form()->text('url')->label('URL')->required()->hint('Example: <br>"#" for top level menu <br>/sample/menu for internal menu <br>https://ombudsman.go.id/ for external URL') !!}
{!! form()->text('icon')->label('Icon')->hint('Visit <a target="_blank" href="https://fomantic-ui.com/elements/icon.html">https://fomantic-ui.com/elements/icon.html</a> for available icons') !!}

<div class="field">
    <label for="">Icon Color</label>
    <div class="ui fluid selection dropdown">
        {!! form()->hidden('color') !!}
        <i class="dropdown icon"></i>
        <div class="text"><div class="ui circular empty label {{ strtolower($menu->color) }}"></div>{{ $menu->color }}</div>
        <div class="menu">
            @foreach($colors as $color)
                <div class="item" data-value="{{ $color }}"><div class="ui circular empty label {{ strtolower($color) }}"></div> {{ $color }}</div>
            @endforeach
        </div>
    </div>
</div>

{!! form()->number('order')->label('Order')->hint('Empty mean last position')->min(1) !!}
{!! form()->dropdown('type', $type)->label('Type')->required() !!}

<h3 class="ui header section dividing">Menu Visibility</h3>
{!! form()->selectMultiple('roles[]', $roles)->label('Hanya tampilkan menu untuk User yang memiliki Roles:') !!}
{{--{!! form()->selectMultiple('permission[]', $permissions)->label('Permissions') !!}--}}

{!! form()->action([
    form()->submit(__('Simpan')),
    form()->link(__('Kembali'), route('menu::menu.index'))
]) !!}
