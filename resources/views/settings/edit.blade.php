<x-volt-app :title="__('Settings')">
    <x-volt-panel title="Global Application Settings">
        {!! form()->put(route('platform::settings.update'))->horizontal() !!}
            {!! form()->make(config('laravolt.platform.settings'))->bindValues($config)->render() !!}

            {!! form()->action(form()->submit(__('Simpan'))) !!}
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
