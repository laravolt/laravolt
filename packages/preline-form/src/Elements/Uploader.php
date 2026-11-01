<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Symfony\Component\Mime\MimeTypes;

/**
 * File field for form()->uploader(). The uploader script loaded by
 * `@laravoltScripts` (resources/js/uploader.js) turns it into a FilePond field.
 *
 * With ajax (the default) each file is uploaded to the media endpoint as soon
 * as it is picked, and the form posts `_<name>`: a JSON list of the file URLs,
 * read with request()->media('<name>'). With ajax(false) the files are posted
 * with the form as `<name>[]`, so the form must be multipart.
 */
class Uploader extends File
{
    protected $ajax = true;

    protected $mediaUrl;

    protected $fallbackMediaUrl = 'media::store';

    /** @var list<array{file: string, name: string, size: int, type: string}> */
    protected array $files = [];

    public function __construct($name)
    {
        parent::__construct($name);

        $this->addClass('uploader');
        $this->setAttribute('data-limit', '1');
        $this->setAttribute('data-file-max-size', '1024');
    }

    /**
     * Maximum size of each file, in MB.
     */
    public function fileMaxSize(int $size)
    {
        return $this->data('file-max-size', (string) $size);
    }

    /**
     * Kept for compatibility with the Semantic UI uploader; not enforced.
     */
    public function maxSize(int $size)
    {
        return $this->data('max-size', (string) $size);
    }

    /**
     * Allowed extensions, without the dot: ['jpg', 'png'].
     */
    public function extensions(array $extensions)
    {
        return $this->data('extensions', implode(',', $extensions));
    }

    public function ajax($ajax = true)
    {
        $this->ajax = (bool) $ajax;

        return $this;
    }

    /**
     * Upload endpoint. Defaults to media::store with the fileuploader handler.
     */
    public function mediaUrl(string $url)
    {
        $this->mediaUrl = $url;

        return $this;
    }

    public function getValue()
    {
        return $this->files;
    }

    public function displayValue()
    {
        if ($this->files === []) {
            return '';
        }

        $items = array_map(
            fn (array $file): string => sprintf(
                '<li><a href="%s" target="_blank" rel="noopener" class="text-blue-600 hover:underline dark:text-blue-500">%s</a></li>',
                e($file['file']),
                e($file['name'])
            ),
            $this->files
        );

        return '<ul class="space-y-1">'.implode('', $items).'</ul>';
    }

    /**
     * Files already attached: media models, URLs, or the JSON list of URLs that
     * the form posts (for example the old input after a failed validation).
     */
    protected function setValue($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : [$value];
        }

        if ($value instanceof Model) {
            $value = [$value];
        }

        if (! is_iterable($value)) {
            return $this;
        }

        $files = [];
        foreach ($value as $media) {
            if ($media instanceof Model) {
                $files[] = [
                    'file' => $media->getFullUrl(),
                    'name' => (string) $media->file_name,
                    'size' => (int) $media->size,
                    'type' => (string) $media->mime_type,
                ];
            } elseif (is_string($media) && $media !== '') {
                $files[] = $this->describeUrl($media);
            }
        }

        $this->files = $files;
        $this->removeAttribute('value');

        if ($files !== []) {
            $this->data('files', $files);
        } else {
            $this->removeAttribute('data-files');
        }

        return $this;
    }

    protected function beforeRender()
    {
        if (! $this->preparedForRender) {
            $url = $this->mediaUrl;

            if (! $url && Route::has($this->fallbackMediaUrl)) {
                $url = URL::route($this->fallbackMediaUrl, ['handler' => 'fileuploader']);
            }

            if ($this->ajax && $url) {
                $this->setAttribute('data-media-url', $url);
            } else {
                $this->removeAttribute('data-media-url');
            }
        }

        return parent::beforeRender();
    }

    /**
     * @return array{file: string, name: string, size: int, type: string}
     */
    protected function describeUrl(string $url): array
    {
        $path = (string) parse_url($url, PHP_URL_PATH);
        $name = rawurldecode(basename($path));
        $extension = mb_strtolower(pathinfo($name, PATHINFO_EXTENSION));

        return [
            'file' => URL::to($url),
            'name' => $name !== '' ? $name : $url,
            'size' => $this->localSize($path),
            'type' => ($extension !== '' ? MimeTypes::getDefault()->getMimeTypes($extension)[0] ?? null : null)
                ?? 'application/octet-stream',
        ];
    }

    /**
     * Size of a file served from public/, or 0 when it is stored elsewhere.
     */
    protected function localSize(string $path): int
    {
        $public = realpath(public_path());
        $file = $public !== false ? realpath(public_path(mb_ltrim(rawurldecode($path), '/'))) : false;

        if ($file === false || ! is_file($file) || ! str_starts_with($file, $public.DIRECTORY_SEPARATOR)) {
            return 0;
        }

        return (int) filesize($file);
    }
}
