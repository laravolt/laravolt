<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Facades\Route;

class Redactor extends TextArea
{
    protected $mediaUrl;

    protected $fallbackMediaUrl = 'media::store';

    public function mediaUrl(string $url)
    {
        $this->mediaUrl = $url;

        return $this;
    }

    public function render()
    {
        $result = parent::render();

        return $result . $this->renderScript();
    }

    protected function beforeRender()
    {
        $url = $this->mediaUrl;

        if (! $url) {
            $url = Route::has($this->fallbackMediaUrl) ? route($this->fallbackMediaUrl) : false;
        }
        if ($url) {
            $this->data('upload-url', $url);
        }
    }

    protected function renderScript()
    {
        return <<<HTML
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Redactor('[data-token="{$this->getAttribute('data-token')}"]');
    });
</script>
HTML;
    }
}
