<?php

namespace Laravolt\Workflow\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravolt\Workflow\Models\CamundaForm;
use Laravolt\Workflow\Models\Segments;
use Laravolt\Workflow\TableView\SegmentTableView;

class SegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return SegmentTableView
     */
    public function index()
    {
        $items = Segments::all();

        return SegmentTableView::make($items)->view('managementcamunda::Segment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $processes = CamundaForm::select('process_definition_key')->distinct()->get();
        $process_options = [];
        foreach ($processes as $process) {
            $process_options[$process->process_definition_key] = $process->process_definition_key;
        }

        return view('managementcamunda::Segment.create', compact('process_options', 'processes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            Segments::create($request->all());
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
        foreach ($request->field_name as $field) {
            $result = CamundaForm::where('process_definition_key', '=', $request->process_definition_key)
                ->where('task_name', '=', $request->task_name)
                ->where('field_name', '=', $field['field_name'])
                ->update([
                    'segment_group' => $request->segment_name,
                    'segment_order' => $request->segment_order,
                    'field_order' => $field['field_order'],
                ]);
        }

        return redirect()->route('segment.index')->withSuccess('Data berhasil Ditambahkan');
    }

    /**
     * Show the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $segment = Segments::where('id', '=', $id)->get();
        $field_name = CamundaForm::where('segment_group', '=', $id)->get();

        return view('managementcamunda::Segment.show', compact('segment', 'field_name'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $data = Segments::where('id', '=', $id)->get();
        $processes = CamundaForm::select('process_definition_key')->distinct()->get();
        $process_options = [];
        foreach ($processes as $process) {
            $process_options[$process->process_definition_key] = $process->process_definition_key;
        }

        return view('managementcamunda::Segment.edit', compact('process_options'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
