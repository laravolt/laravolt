@if($this->filters())
    <div data-role="suitable-filter">
        <div class="ui basic button icon" data-role="suitable-filter-icon">
            <i class="icon filter"></i>
            <i class="icon angle down"></i>
        </div>
        <div class="ui popup p-0" style="min-width: 300px" wire:ignore.self>
            <form class="ui form p-2" wire:submit.prevent>
                @foreach($this->filters() as $filter)
                    {!! $filter->render() !!}
                @endforeach
            </form>
            <x-volt-button wire:click="resetFilters" type="reset" class="bottom basic fluid attached b-0"
                           icon="times circle outline">
                Clear Filter
            </x-volt-button>
        </div>
    </div>

    @once
        @push('main')
            <script>
                $(function () {
                    $('[data-role="suitable-filter-icon"]')
                        .popup({
                            inline: true,
                            on: 'click',
                            position: 'bottom right',
                            lastResort: 'bottom left',
                        })
                    ;
                    $('[data-role="suitable-filter"] button[type="reset"]')
                        .on('click', function () {
                            $('[data-role="suitable-filter"] form').form('clear');
                            $('[data-role="suitable-filter"] .ui.dropdown').dropdown('set selected', '0');
                        });
                });
            </script>
        @endpush
    @endonce
@endif
