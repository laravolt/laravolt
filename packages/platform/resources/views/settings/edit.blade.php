<x-laravolt::layout.app :title="__('Settings')">
    <x-laravolt::panel title="Global Application Settings">
        {!! form()->put()->horizontal() !!}
        {!! form()->make(config('laravolt.platform.settings'))->bindValues(config('laravolt.ui'))->render() !!}
        {!! form()->action(form()->submit(__('Simpan'))) !!}
        {!! form()->close() !!}
    </x-laravolt::panel>
</x-laravolt::layout.app>
