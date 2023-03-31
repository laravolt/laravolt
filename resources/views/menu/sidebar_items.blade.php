@foreach($items->sortBy(fn($item) => $item->data('order')) as $item)
    @if($item->hasChildren())
        <div class="title title__1 item {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) }}">
            {!! svg(config('laravolt.ui.iconset').'-'.$item->data('icon'), null, ['class' => 'left x-icon'])
            ->width('16px')
            ->toHtml() !!}
            <span>{{ $item->title }}</span>
            <i class="angle down icon"></i>
        </div>
        <div class="content content__2 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) }} ">
            <div class="ui list">
                @foreach($item->children()->sortBy(fn($item) => $item->data('order')) as $child)
                    @if($child->hasChildren())
                        <div class="title title__2 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($child->children(), $child->isActive) }}">
                            <span>{{ $child->title }}</span>
                            <i class="angle down icon"></i>
                        </div>
                        <div class="content content__3 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($child->children(), $child ->isActive) }}">
                            <div class="ui list list__3">
                                @foreach($child->children() as $grandchild)
                                    <a class="title title__3 empty {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($grandchild->children(), $grandchild->isActive) }}"
                                       href="{{ $grandchild->url() }}"
                                       data-parent="{{ $grandchild->parent()->title }}">
                                        <span>{{ $grandchild->title }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $child->url() }}" data-parent="{{ $child->parent()->title }}"
                           class="item title__2 {{ ($child->isActive)?'selected':'' }} ">{{ $child->title }}</a>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <a class="title title__1 item empty {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) }}"
           href="{{ $item->url() }}"
           data-parent="{{ $item->parent()->title }}">
            {!! svg(config('laravolt.ui.iconset').'-'.$item->data('icon'), null, ['class' => 'left x-icon'])
            ->width('16px')
            ->toHtml() !!}
            <span>{{ $item->title }}</span>
        </a>
        <div class="content"></div>
    @endif
@endforeach
