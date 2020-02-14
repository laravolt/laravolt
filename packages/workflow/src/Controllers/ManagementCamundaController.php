<?php

namespace Laravolt\Workflow\Controllers;

use DB;
use Http\Client\Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravolt\Workflow\Models\CamundaForm;
use Laravolt\Workflow\Services\FormAdapter\FormAdapter;
use Laravolt\Workflow\Services\FormFieldsExport;
use Laravolt\Workflow\TableView\ManagementCamundaTableView;
use Laravolt\Workflow\Traits\DataRetrieval;
use Maatwebsite\Excel\Excel;

class ManagementCamundaController extends Controller
{
    use DataRetrieval;

    public function index()
    {
        $items = CamundaForm::autoFilter()->search(request('search'))->latest()->paginate();

        return (ManagementCamundaTableView::make($items))->view('managementcamunda::new');
    }

    public function create()
    {
        $processes = CamundaForm::select('process_definition_key')->distinct()->get();
        $process_options = [];
        foreach ($processes as $process) {
            $process_options[$process->process_definition_key] = $process->process_definition_key;
        }
        $type_options = FormAdapter::$types;
        $forms = collect(config('workflow.forms'))->keys();
        $forms = $forms->combine($forms)->toArray();

        return view('managementcamunda::create', compact('process_options', 'type_options', 'forms'));
    }

    public function store(Request $request)
    {
        $field_meta = [];
        $field_meta['editable'] = $request->editable;
        $field_meta['validation'] = $request->validation;
        $attributes = [];
        if ($request->visibility) {
            foreach ($request->visibility as $field) {
                array_push($attributes, $field['field_name']."=='".$field['field_value']."'");
            }
            $field_meta['attributes'] = ['v-show' => implode(' && ', $attributes)];
        }

        if ($request->field_type == 'multirow') {
            $field_meta['form'] = $request->field_type;
        }
        $request->request->add(['form_name' => $request->task_name]);
        $request->request->add(['field_meta' => json_encode($field_meta)]);

        try {
            CamundaForm::create($request->all());

            return redirect()->route('managementcamunda.create')
                ->withSuccess('Field Berhasil Ditambah! ');
        } catch (Exception $e) {
            return redirect()->route('managementcamunda.create')
                ->withErrors('Data sudah ada! ');
        }
    }

    public function show($id)
    {
        $data = CamundaForm::where('id', '=', $id)->first();
        $types = CamundaForm::select('field_type')
            ->distinct()
            ->get();
        $type_options = [];
        foreach ($types as $type) {
            $type_options[$type->field_type] = $type->field_type;
        }
        $field_meta = json_decode($data['field_meta']);

        return view('managementcamunda::show', compact('type_options', 'data', 'field_meta'));
    }

    public function edit($id)
    {
        $type_options = FormAdapter::$types;
        $data = CamundaForm::where('id', '=', $id)->first();
        $fields = CamundaForm::where('task_name', '=', $data->task_name)->get();
        $field_meta = json_decode($data['field_meta']);
        $forms = collect(config('workflow.forms'))->keys();
        $forms = $forms->combine($forms)->toArray();

        return view('managementcamunda::edit', compact('type_options', 'data', 'field_meta', 'fields', 'forms'));
    }

    public function update(Request $request, $id)
    {
        $field_meta = [];
        $field_meta['editable'] = $request->editable;
        $field_meta['validation'] = $request->validation;
        $field_meta['dependency'] = $request->dependency;
        $attributes = [];
        if ($request->visibility) {
            foreach ($request->visibility as $field) {
                array_push($attributes, $field['field_name']."=='".$field['field_value']."'");
            }
            $field_meta['attributes'] = ['v-show' => implode(' && ', $attributes)];
        }
        if ($request->field_type == 'multirow') {
            $field_meta['form'] = $request->field_type;
        }
        $request->request->add(['field_meta' => json_encode($field_meta)]);
        CamundaForm::where('id', '=', $id)
            ->update($request->except([
                '_token', '_method', 'editable', 'dependency', 'validation', 'visibility', 'form',
            ]));

        return redirect()->route('managementcamunda.index')
            ->withSuccess('Field Berhasil Diubah! ');
    }

    public function destroy($id)
    {
        CamundaForm::destroy($id);

        return redirect()->route('managementcamunda.index')
            ->withSuccess('Field Berhasil Dihapus! ');
    }

    public function getTasks(Request $request)
    {
        $tasks = DB::table('camunda_form')
            ->where('process_definition_key', '=', $request->process)
            ->pluck('task_name', 'task_name')->sort();

        return $tasks;
    }

    public function getFields(Request $request)
    {
        if ($request->has('process')) {
            $fields = $this->getFieldsByProcessDefinitionKey($request->process);
        } else {
            $fields = DB::table('camunda_form')
                ->where('task_name', '=', $request->task)
                ->pluck('field_name', 'field_name')
                ->sort();
        }

        return $fields;
    }

    public function getAttributes(Request $request)
    {
        $fields_label = CamundaForm::select('field_type')
            ->where('field_name', '=', $request->field)
            ->get();

        return $fields_label;
    }

    public function download()
    {
        $filename = sprintf('simpel-form-%s.csv', now());

        return (new FormFieldsExport())->download($filename, Excel::CSV);
    }
}
