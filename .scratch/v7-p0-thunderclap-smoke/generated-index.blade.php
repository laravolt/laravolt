<x-volt-app title="{{ __('Item') }}" :isShowTitleBar="false">
    <div class="flex justify-between items-center gap-x-3">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-neutral-200">
            {{ __('Item') }}
        </h1>

        <div class="flex justify-end items-center gap-x-2">
            <x-volt-link-button icon="plus" :url="route('modules::item.create')" :label="__('laravolt::action.add')" />
        </div>
    </div>

    @livewire(\Modules\Item\ItemTableView::class)
</x-volt-app>
