<?php
declare(strict_types=1);
namespace Laravolt\Platform\Components;
use Illuminate\View\Component;

class FileUploadProgress extends Component
{
    public string $id;
    public string $fileName;
    public int $progress;
    public string $fileSize;
    public string $status;

    public function __construct(?string $id = null, ?string $fileName = null, ?int $progress = null, ?string $fileSize = null, ?string $status = null)
    {
        $this->id = $id ?? 'file-progress-' . uniqid();
        $this->fileName = $fileName ?? 'file.pdf';
        $this->progress = $progress ?? 0;
        $this->fileSize = $fileSize ?? '0 KB';
        $this->status = $status ?? 'uploading';
    }

    public function render() { return view('laravolt::components.file-upload-progress'); }
}
