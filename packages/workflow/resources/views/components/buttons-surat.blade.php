<?php

$surat = collect();
$tasks = collect($tasks);
if ($tasks->isNotEmpty()) {
    $surat = \App\TemplateSurat::whereProcessDefinitionKey($tasks->first()->process_definition_key)
        ->whereIn('task_name', $tasks->pluck('task_name'))
        ->get()->sortBy('name');
}
?>

@if($surat->isEmpty())
    <div class="ui labeled icon top left pointing dropdown primary small button" dropdown-cetak-surat>
        <i class="print icon"></i>
        <span class="text">{{ $label ?? 'Cetak Surat...' }}</span>
        <div class="menu">
            <div class="item">
                Belum ada surat yang bisa dicetak
            </div>
        </div>
    </div>
@else
<div class="ui labeled icon top left pointing dropdown primary small button" dropdown-cetak-surat>
    <i class="print icon"></i>
    <span class="text">{{ $label ?? 'Cetak Surat...' }}</span>
    <div class="menu">
        <div class="ui search icon input">
            <i class="search icon"></i>
            <input type="text" name="search" placeholder="Cari Surat..">
        </div>
        <div class="menu scrolling">
            @foreach($surat as $item)
                <div class="item" data-value="{{ route('workflow::print.index', ['module' => $module->id, 'templateId' => $item->getKey(), 'processInstanceId' => $processInstance->id]) }}">
                    <div class="ui {{ $item->color }} empty circular basic label mini"></div>
                    {{ $item->name }}
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('body')
    <script>
        $(function () {
            $('[dropdown-cetak-surat]').dropdown({
                action: function (text, value, elm) {
                    window.location.href = value;
                }
            });
        });
    </script>
@endpush
@endif
