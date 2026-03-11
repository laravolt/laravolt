<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

/**
 * FilePond file upload component.
 *
 * Renders a FilePond-enhanced file input. Requires FilePond JS/CSS assets
 * to be loaded in the layout (https://pqina.nl/filepond).
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
        $id = $this->getAttribute('id') ?? 'filepond_'.md5($name);

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
              if (!inputEl || typeof FilePond === 'undefined') return;

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
            $options['server'] = $this->serverUrl;
            $options['instantUpload'] = $this->instantUpload;
        }

        if (! $this->allowImagePreview) {
            $options['allowImagePreview'] = false;
        }

        return json_encode($options, JSON_UNESCAPED_SLASHES);
    }
}
