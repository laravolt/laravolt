<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Traits;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait DataRetrieval
{
    public function getDataByProcessInstanceId($processInstanceId)
    {
        $mapping = DB::table('camunda_task')
            ->where('process_instance_id', $processInstanceId)
            ->oldest()
            ->get(['form_type', 'form_id']);

        $data = [];

        foreach ($mapping as $map) {
            $formData = collect((array) DB::table($map->form_type)->find($map->form_id))
                ->except(['id', 'created_at', 'created_by', 'updated_at', 'updated_by'])
                ->toArray();
            $data += $formData;
        }

        return $data;
    }

    public function getGlobalVariables()
    {
        $now = now();

        return [
            '_D' => $now->isoFormat('D'),
            '_M' => $now->isoFormat('M'),
            '_YYYY' => $now->isoFormat('YYYY'),
            '_L' => $now->isoFormat('L'),
            '_LL' => $now->isoFormat('LL'),
            '_LLL' => $now->isoFormat('LLL'),
            '_LLLL' => $now->isoFormat('LLLL'),
        ];
    }

    public function getUserVariables(User $user)
    {
        return [
            '_NAMA_KANTOR' => $user->kantor->nama_kantor,
            '_KODE_KANTOR' => $user->kantor->kode_kantor,
        ];
    }

    public function getFieldsByProcessDefinitionKey($processDefinitionKey)
    {
        $tables = DB::table('camunda_form')
            ->where('process_definition_key', $processDefinitionKey)
            ->oldest()
            ->pluck('form_name');

        $fields = [];

        foreach ($tables as $table) {
            $fieldsData = Schema::getColumnListing($table);
            $fields += $fieldsData;
        }

        $globalFields = ['_NAMA_KANTOR', '_KODE_KANTOR', '_D', '_M', '_YYYY', '_L', '_LL'];

        return collect($fields)
            ->combine($fields)
            ->except(['created_by', 'updated_by', 'created_at', 'updated_at', 'id'])
            ->filter()
            ->reject(function ($item) {
                return Str::contains($item, ' ');
            })
            ->merge($globalFields)
            ->transform(function ($item) {
                return sprintf('{{ $%s }}', $item);
            })
            ->sort();
    }
}
