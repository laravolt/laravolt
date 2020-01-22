<div class="ui accordion sidebar__accordion" data-role="sidebar-accordion">
    @foreach($items as $item)
        @if($item->hasChildren())
            <div class="title {{ \Laravolt\Platform\Services\Menu::setActiveParent($item->children(), $item->link->isActive) }}">
                <i class="left icon {{ $item->data('icon') }}"></i>
                <span>{{ $item->title }}</span>
                <i class="angle down icon"></i>
            </div>
            <div class="content {{ \Laravolt\Platform\Services\Menu::setActiveParent($item->children(), $item->link->isActive) }} ">
                @if($item->hasChildren())
                    <div class="ui list">
                        @foreach($item->children() as $child)
                            <a href="{{ $child->url() }}" data-parent="{{ $child->parent()->title }}"
                               class="item {{ ($child->link->isActive)?'active':'' }} ">{{ $child->title }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <a class="title empty {{ \Laravolt\Platform\Services\Menu::setActiveParent($item->children(), $item->link->isActive) }}"
               href="{{ $item->url() }}"
               data-parent="{{ $item->parent()->title }}">
                <i class="left icon {{ $item->data('icon') }}"></i>
                <span>{{ $item->title }}</span>
            </a>
            <div class="content"></div>
        @endif

    @endforeach
</div>
