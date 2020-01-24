<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Controllers;

use App\Enums\JenisTemplateSurat;
use App\TemplateSurat;
use HTMLtoOpenXML\Parser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Laravolt\Camunda\Models\ProcessInstanceHistory;
use Laravolt\Jasper\Jasper;
use Laravolt\Workflow\Entities\Module;
use Laravolt\Workflow\Traits\DataRetrieval;
use PhpOffice\PhpWord\TemplateProcessor;

class PrintController extends Controller
{
    use AuthorizesRequests;
    use DataRetrieval;

    public function index(Module $module, $processInstanceId, $templateId)
    {
        $this->authorize('create', $module->getModel());

        $processInstance = (new ProcessInstanceHistory($processInstanceId))->fetch();
        $completedTasks = app('laravolt.workflow')->completedTasks($processInstanceId, $module->getTasks());

        $template = TemplateSurat::findOrFail($templateId);

        switch ($template->type) {
            case JenisTemplateSurat::STANDARD:
                $path = $this->generateTemplateStandard($template, $processInstance);

                break;
            case JenisTemplateSurat::JASPER_PATH:
                $path = $this->generateTemplateJasper($template, $processInstance);

                break;
        }
        $extension = pathinfo($path)['extension'] ?? 'docx';

        return view('workflow::print.index',
            compact('path', 'module', 'processInstance', 'completedTasks', 'template', 'extension'));
    }

    protected function generateTemplateJasper($template, $processInstance)
    {
        $path = $template->body;
        $path = '/reports/'.ltrim($path, '/');
        $format = request('format', 'pdf');

        $path = sprintf('%s.%s', $path, $format);
        $jasper = app(Jasper::class);
        $queryString['process_instance_id'] = $processInstance->id;

        $response = $jasper->get($path, ['query' => $queryString]);

        $assetPath = sprintf('surat/%s/%s-%s.%s', $template->process_definition_key, $processInstance->id, now()->format('YmdHis'), $format);
        $storagePath = sprintf('app/public/%s', $assetPath);
        $directory = dirname(storage_path($storagePath));

        if (!is_dir($directory)) {
            \File::makeDirectory($directory, 0755, true);
        }

        \File::put(storage_path($storagePath), (string) $response);

        return 'storage/'.$assetPath;
    }

    protected function generateTemplateStandard($template, $processInstance)
    {
        $data = $this->getGlobalVariables() + $this->getUserVariables(auth()->user()) + $this->getDataByProcessInstanceId($processInstance->id);
        Storage::disk('surat-compiled')->put("{$template->getKey()}.blade.php", $template->body);
        $content = view("surat-compiled::{$template->getKey()}", $data)->render();

        return $this->generateDocx($processInstance->id, $template, $content);
    }

    protected function generateDocx($processInstanceId, $template, $content)
    {
        $parser = new Parser();
        $content = $parser->fromHTML($content);

        $document = new TemplateProcessor(resource_path('report-templates/general.docx'));
        $document->setValues([
            'nomor' => '1234',
            'perihal' => '-',
            'lampiran' => '-',
            'yth_nama' => 'Bayu Hendra',
            'yth_di' => 'Sragen',
            'ttd_nama' => setting('surat.ttd_nama'),
            'ttd_jabatan' => setting('surat.ttd_jabatan'),
            'content' => $content,
        ]);

        $assetPath = sprintf('surat/%s/%s-%s.docx', $template->process_definition_key, $processInstanceId, now()->format('YmdHis'));
        $storagePath = sprintf('app/public/%s', $assetPath);
        $directory = dirname(storage_path($storagePath));

        if (!is_dir($directory)) {
            \File::makeDirectory($directory, 0755, true);
        }

        $document->saveAs(storage_path($storagePath));

        return 'storage/'.$assetPath;

        // download file
        // $tempFile = $template->save("php://output");
        // header('Content-Type: application/octet-stream');
        // header("Content-Disposition: attachment; filename=sample.docx;");
        // header('Content-Length: ' . filesize($tempFile));
        // readfile($tempFile);
        // session_write_close();
        // exit(0);
    }
}
