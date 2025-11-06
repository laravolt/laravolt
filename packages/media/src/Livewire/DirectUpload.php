<?php

declare(strict_types=1);

namespace Laravolt\Media\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Laravolt\Platform\Models\Guest;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class DirectUpload extends Component
{
    use WithFileUploads;

    public $file;

    public $uploadedFiles = [];
    
    public $disk = 's3';
    
    public $collection = 'default';
    
    public $maxFileSize = 102400; // 100MB in KB

    public function mount($disk = 's3', $collection = 'default', $maxFileSize = 102400)
    {
        $this->disk = $disk;
        $this->collection = $collection;
        $this->maxFileSize = $maxFileSize;
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => ['required', 'file', 'max:' . $this->maxFileSize],
        ]);

        try {
            /** @var \Spatie\MediaLibrary\InteractsWithMedia $user */
            $user = auth()->user() ?? Guest::first();

            // Get the temporary file path
            /** @var TemporaryUploadedFile $temporaryFile */
            $temporaryFile = $this->file;
            
            // Add media directly to the specified disk
            $media = $user->addMedia($temporaryFile->getRealPath())
                ->usingName($temporaryFile->getClientOriginalName())
                ->usingFileName($temporaryFile->getClientOriginalName())
                ->toMediaCollection($this->collection, $this->disk);

            // Add to uploaded files list
            $this->uploadedFiles[] = [
                'id' => $media->getKey(),
                'name' => $media->file_name,
                'size' => $media->size,
                'type' => $media->mime_type,
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl(),
            ];

            // Clear the file input
            $this->reset('file');

            // Dispatch success event
            $this->dispatch('fileUploaded', $media->getKey());
        } catch (FileCannotBeAdded $e) {
            $this->addError('file', $e->getMessage());
            report($e);
        } catch (\Exception $e) {
            $this->addError('file', 'Failed to upload file: ' . $e->getMessage());
            report($e);
        }
    }

    public function removeFile($id)
    {
        try {
            /** @var \Illuminate\Database\Eloquent\Model $modelClass */
            $modelClass = config('media-library.media_model');
            /** @var \Spatie\MediaLibrary\MediaCollections\Models\Media */
            $media = app($modelClass)::query()->findOrFail($id);
            $media->delete();

            // Remove from uploaded files list
            $this->uploadedFiles = collect($this->uploadedFiles)
                ->reject(fn($file) => $file['id'] === $id)
                ->values()
                ->toArray();

            // Dispatch delete event
            $this->dispatch('fileRemoved', $id);
        } catch (\Exception $e) {
            $this->addError('file', 'Failed to remove file: ' . $e->getMessage());
            report($e);
        }
    }

    public function render()
    {
        return view('laravolt::media.livewire.direct-upload');
    }
}
