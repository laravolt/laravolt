<div data-role="suitable-filter">
    <div class="ui basic button icon" data-role="suitable-filter-icon">
        <i class="icon filter"></i>
        <i class="icon angle down"></i>
    </div>
    <div class="ui popup p-0">
        <div class="ui form p-2">
            {!! form()->checkboxGroup('name', ['Admin', 'Manager', 'Staff'])->label('Roles') !!}
            {!! form()->radioGroup('status', ['Aktif', 'Pending', 'Blocked'])->label('Status') !!}
            {!! form()->datepicker('date')->label('Terdaftar')->attributes(['wire:click' => 'resetPage']) !!}
            {!! form()->dropdownDB('users', 'select * from users limit 10')->label('Pengguna') !!}
        </div>
        <x-laravolt::button class="bottom secondary fluid attached b-0" icon="times circle outline">Clear Filter</x-laravolt::button>
    </div>
</div>

@push('script')
    <script>
        $(function () {
            $('[data-role="suitable-filter-icon"]')
                .popup({
                    inline: true,
                    on: 'click',
                    position: 'bottom right'
                })
            ;
        });
    </script>
@endpush
