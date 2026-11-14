<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Facades\Route;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class Redactor extends TextArea
{
    protected $mediaUrl;

    protected $fallbackMediaUrl = 'media::store';

    public function mediaUrl(string $url)
    {
        $this->mediaUrl = $url;

        return $this;
    }

    /**
     * Redactor stores rich text, so the display table keeps its formatting but
     * drops anything that can run script (event handlers, script, javascript: links).
     */
    public function displayValue()
    {
        $value = $this->getValue();

        if (! is_string($value)) {
            return parent::displayValue();
        }

        $config = (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowRelativeMedias();

        return (new HtmlSanitizer($config))->sanitize($value);
    }

    public function render()
    {
        $result = parent::render();

        return $result.$this->renderScript();
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
        let uploadUrl = document.querySelector('[data-token="{$this->getAttribute('data-token')}"]').dataset.uploadUrl;

        Redactor('[data-token="{$this->getAttribute('data-token')}"]', {
            minHeight: '100px',
            toolbar: {
                stickyTopOffset: 60
            },
            image: {
                upload: uploadUrl
            }
        });
    });
</script>
HTML;
    }
}
