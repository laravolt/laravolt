<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

use RuntimeException;

/**
 * FilePond file upload component.
 *
 * Renders a FilePond-enhanced file input (https://pqina.nl/filepond).
 * `@laravoltScripts` loads the FilePond shipped with Laravolt on demand; a copy
 * the layout already loaded itself is used as is. Layouts without that directive
 * must load FilePond themselves.
 *
 * Without server() the files are posted with the form, so the form must be
 * multipart. With server() on this site, FilePond sends the session CSRF token
 * as X-CSRF-TOKEN, so a route in the web group accepts the upload.
 *
 * Usage:
 *   {!! Form::filepond('avatar') !!}
 *   {!! Form::filepond('documents')->multiple()->accept(['application/pdf']) !!}
 *   {!! Form::filepond('photo')->label('Profile Photo')->maxFileSize('5MB') !!}
 *   {!! Form::filepond('files')->server('/upload')->instantUpload() !!}
 */
class Filepond extends FormControl
{
    protected $errorMessage = '';

    protected $isMultiple = false;

    protected $acceptedTypes = [];

    protected $maxFileSizeValue = null;

    protected $maxFilesValue = null;

    protected $serverUrl = null;

    protected $instantUpload = false;

    protected $allowImagePreview = true;

    public function __construct($name)
    {
        parent::__construct($name);
    }

    /**
     * Allow multiple file uploads.
     */
    public function multiple(bool $multiple = true): static
    {
        $this->isMultiple = $multiple;

        return $this;
    }

    /**
     * Restrict accepted MIME types or extensions.
     * Example: ->accept(['image/*', 'application/pdf'])
     */
    public function accept(array $types): static
    {
        $this->acceptedTypes = $types;

        return $this;
    }

    /**
     * Set maximum file size. Example: '5MB', '500KB'.
     */
    public function maxFileSize(string $size): static
    {
        $this->maxFileSizeValue = $size;

        return $this;
    }

    /**
     * Set maximum number of files (only relevant when multiple() is used).
     */
    public function maxFiles(int $count): static
    {
        $this->maxFilesValue = $count;

        return $this;
    }

    /**
     * Set the FilePond server endpoint for instant/async uploads.
     * When set, files are uploaded immediately upon selection.
     */
    public function server(string $url): static
    {
        $this->serverUrl = $url;

        return $this;
    }

    /**
     * Enable instant upload mode (requires server() to be configured).
     */
    public function instantUpload(bool $instant = true): static
    {
        $this->instantUpload = $instant;

        return $this;
    }

    /**
     * Disable the image preview plugin.
     */
    public function withoutImagePreview(): static
    {
        $this->allowImagePreview = false;

        return $this;
    }

    public function setError($message = '')
    {
        parent::setError();
        $this->errorMessage = $message;

        return $this;
    }

    public function hasError()
    {
        return parent::hasError();
    }

    public function render()
    {
        if ($this->label) {
            return $this->renderField();
        }

        return $this->renderControl();
    }

    protected function getError()
    {
        return $this->errorMessage;
    }

    protected function renderControl(): string
    {
        $name = $this->getAttribute('name');
        $inputName = $this->isMultiple ? rtrim($name, '[]').'[]' : $name;
        $id = $this->getAttribute('id') ?? 'filepond_'.hash('sha256', $name);

        // Build data attributes for FilePond config
        $dataAttrs = '';

        if ($this->maxFileSizeValue !== null) {
            $dataAttrs .= sprintf(' data-max-file-size="%s"', htmlspecialchars($this->maxFileSizeValue, ENT_QUOTES));
        }

        if ($this->maxFilesValue !== null) {
            $dataAttrs .= sprintf(' data-max-files="%d"', $this->maxFilesValue);
        }

        if (! empty($this->acceptedTypes)) {
            $dataAttrs .= sprintf(' data-accepted-file-types="%s"', htmlspecialchars(implode(',', $this->acceptedTypes), ENT_QUOTES));
        }

        if ($this->serverUrl !== null) {
            $dataAttrs .= sprintf(' data-server="%s"', htmlspecialchars($this->serverUrl, ENT_QUOTES));
        }

        $multipleAttr = $this->isMultiple ? ' multiple' : '';
        $allowPreview = $this->allowImagePreview ? 'true' : 'false';

        $input = sprintf(
            '<input type="file" id="%s" name="%s" class="filepond"%s%s>',
            $id,
            htmlspecialchars($inputName, ENT_QUOTES),
            $multipleAttr,
            $dataAttrs
        );

        $script = $this->renderInitScript($id, $allowPreview);

        return $input.$script;
    }

    protected function renderInitScript(string $id, string $allowPreview): string
    {
        $optionsJson = $this->buildFilePondOptions();

        return <<<JS
        <script>
          (function() {
            document.addEventListener('DOMContentLoaded', function() {
              const inputEl = document.getElementById('{$id}');
              if (!inputEl) return;

              // An uploader.js extracted before v8.3.2 has no ready().
              const loader = window.LaravoltUploader;
              const ready = loader && loader.ready ? loader.ready() : Promise.resolve();

              ready.then(function() {
                if (typeof FilePond === 'undefined') return;

                const plugins = [];
                if ({$allowPreview} && typeof FilePondPluginImagePreview !== 'undefined') {
                  plugins.push(FilePondPluginImagePreview);
                }
                if (typeof FilePondPluginFileValidateType !== 'undefined') {
                  plugins.push(FilePondPluginFileValidateType);
                }
                if (typeof FilePondPluginFileValidateSize !== 'undefined') {
                  plugins.push(FilePondPluginFileValidateSize);
                }
                if (plugins.length > 0) {
                  FilePond.registerPlugin(...plugins);
                }

                FilePond.create(inputEl, {$optionsJson});
              }).catch(function(e) {
                console.error('[laravolt filepond]', e);
              });
            });
          })();
        </script>
        JS;
    }

    protected function buildFilePondOptions(): string
    {
        $options = [];

        if ($this->isMultiple) {
            $options['allowMultiple'] = true;
        }

        if ($this->maxFileSizeValue !== null) {
            $options['maxFileSize'] = $this->maxFileSizeValue;
        }

        if ($this->maxFilesValue !== null) {
            $options['maxFiles'] = $this->maxFilesValue;
        }

        if (! empty($this->acceptedTypes)) {
            $options['acceptedFileTypes'] = $this->acceptedTypes;
        }

        if ($this->serverUrl !== null) {
            $options['server'] = $this->serverOption();
            $options['instantUpload'] = $this->instantUpload;
        } else {
            // FilePond posts nothing for a file it has not uploaded unless told
            // to keep it in the file input.
            $options['storeAsFile'] = true;
        }

        if (! $this->allowImagePreview) {
            $options['allowImagePreview'] = false;
        }

        return json_encode($options, JSON_UNESCAPED_SLASHES);
    }

    /**
     * FilePond sends no CSRF token of its own, so a Laravel route in the web
     * group answers 419. Send the session token as X-CSRF-TOKEN, but only to
     * this site; another origin must never receive it.
     *
     * @return string|array{url: string, headers: array<string, string>}
     */
    protected function serverOption(): string|array
    {
        $token = $this->csrfToken();

        if ($token === null || ! $this->isSameOrigin($this->serverUrl)) {
            return $this->serverUrl;
        }

        return [
            'url' => $this->serverUrl,
            'headers' => ['X-CSRF-TOKEN' => $token],
        ];
    }

    protected function csrfToken(): ?string
    {
        try {
            $token = csrf_token();
        } catch (RuntimeException) {
            return null;
        }

        return is_string($token) && $token !== '' ? $token : null;
    }

    protected function isSameOrigin(string $url): bool
    {
        // Browsers read "/\host" like "//host", so treat any backslash as foreign.
        if (str_contains($url, '\\')) {
            return false;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            return false;
        }

        if (! isset($parts['host'])) {
            // A relative URL stays on this site; "javascript:" and the like do not.
            return ! isset($parts['scheme']);
        }

        $request = request();
        $scheme = mb_strtolower($parts['scheme'] ?? $request->getScheme());
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        return $scheme === $request->getScheme()
            && mb_strtolower($parts['host']) === mb_strtolower($request->getHost())
            && (int) $port === (int) $request->getPort();
    }
}
