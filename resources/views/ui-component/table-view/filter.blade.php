@if($this->filters())
    <div class="hs-dropdown [--trigger:click] relative inline-flex">
        <button type="button" class="hs-dropdown-toggle inline-flex items-center gap-x-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm hover:bg-gray-50">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M4 8h16M6 12h12M7 16h10M9 20h6"/></svg>
            <span>Filter</span>
            <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="hs-dropdown-menu transition-[opacity,margin] hs-dropdown-open:opacity-100 opacity-0 hidden z-10 mt-2 w-72 rounded-xl border border-gray-200 bg-white p-3 shadow-md" aria-labelledby="hs-table-filter">
            <form class="space-y-3" wire:submit.prevent>
                @foreach($this->filters() as $filter)
                    {!! $filter->render() !!}
                @endforeach
            </form>
            <div class="mt-3">
                <x-volt-button wire:click="resetFilters" type="reset" class="w-full bg-gray-100 text-gray-700 hover:bg-gray-200" icon="close">
                    Clear Filter
                </x-volt-button>
            </div>
        </div>
    </div>
@endif
