<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Services;

use Illuminate\Support\Facades\Schema;
use Laravolt\Workflow\Models\Form;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FormFieldsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function collection()
    {
        return Form::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return Schema::getColumnListing('camunda_form');
    }

    public function map($row): array
    {
        $data = $row->toArray();

        if ($data['field_meta']) {
            $data['field_meta'] = trim(json_encode($data['field_meta']));
        }

        if ($data['field_select_query'] && in_array($data['field_type'], ['radio', 'dropdown'])) {
            $data['field_select_query'] = trim(json_encode($data['field_select_query']));
        }

        if (!$data['field_order']) {
            $data['field_order'] = 1;
        }

        return $data;
    }
}
