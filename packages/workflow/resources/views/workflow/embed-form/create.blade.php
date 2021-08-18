<x-volt-public>
    <x-volt-panel :title="request('_title', __('Mulai Proses Baru'))" :icon="request('_icon', 'rocket')">
        <div class="ui inverted dimmer">
            <div class="ui text loader">Processing...</div>
        </div>
        {!! $module->startForm(route('workflow::form.store', ['module' => $module->id]), request()->all()) !!}
    </x-volt-panel>

    @once
        @push('script')
            <script>
                document.addEventListener("DOMContentLoaded", function (event) {
                    $('[data-role="start-form"]').on('submit', function (e) {
                        $(this).prev().addClass('active');
                        window.scrollTo(0, 0);
                    })
                });
            </script>
        @endpush
    @endonce
</x-volt-public>

