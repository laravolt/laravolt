@if (!$items->isEmpty())
    @foreach ($items as $item)
        @if ($item->hasChildren())
            @if (!empty($item->title))
                <li
                    class="pt-5 px-5 lg:px-8 mt-5 border-t border-gray-200 first:border-transparent first:pt-0 dark:border-neutral-700 dark:first:border-transparent">
                    <span class="block text-xs uppercase text-gray-500 dark:text-neutral-500">
                        {{ $item->title }}
                    </span>
                </li>
            @endif
            @include('laravolt::menu.sidebar_items', ['items' => $item->children()])
        @else
            <div>
                <a class="flex items-center gap-x-2 px-3 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-neutral-700 {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) }}"
                    href="{{ $item->url() }}">
                    <i class="{{ $item->data('icon') }}"></i>
                    <span>{{ $item->title }}</span>
                </a>
            </div>
        @endif
    @endforeach
@endif
