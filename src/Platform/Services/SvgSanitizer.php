<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Reduces an SVG document to drawing markup that is safe to inline in a page:
 * an allowlist of elements, no event handlers, no scripts, and references only
 * to fragments inside the document (or embedded raster images).
 */
final class SvgSanitizer
{
    public const MAX_BYTES = 512 * 1024;

    /** Lower-cased local names of the elements that are kept. */
    private const ELEMENTS = [
        'svg', 'g', 'defs', 'symbol', 'use', 'title', 'desc', 'style',
        'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
        'text', 'tspan', 'textpath', 'image',
        'lineargradient', 'radialgradient', 'stop', 'pattern', 'clippath', 'mask', 'marker',
        'filter', 'feblend', 'fecolormatrix', 'fecomponenttransfer', 'fecomposite', 'fedropshadow',
        'feflood', 'fefunca', 'fefuncb', 'fefuncg', 'fefuncr', 'fegaussianblur', 'femerge',
        'femergenode', 'femorphology', 'feoffset',
    ];

    private const RASTER_DATA_URI = '#^data:image/(png|jpe?g|gif|webp);base64,[a-z0-9+/=\s]+$#i';

    public function sanitize(string $svg): ?string
    {
        if ($svg === '' || strlen($svg) > self::MAX_BYTES || stripos($svg, '<!ENTITY') !== false) {
            return null;
        }

        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $loaded = $document->loadXML($svg, LIBXML_NONET);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $root = $document->documentElement;
        if (! $loaded || ! $root instanceof DOMElement || strtolower($root->localName) !== 'svg') {
            return null;
        }

        $this->clean($root);

        $output = $document->saveXML($root);

        return $output === false ? null : $output;
    }

    private function clean(DOMElement $element): void
    {
        foreach (iterator_to_array($element->attributes ?? [], false) as $attribute) {
            /** @var DOMAttr $attribute */
            if (! $this->keepsAttribute($element, $attribute)) {
                $element->removeAttributeNode($attribute);
            }
        }

        /** @var list<DOMNode> $children */
        $children = iterator_to_array($element->childNodes, false);
        foreach ($children as $child) {
            if ($child instanceof DOMElement) {
                if (in_array(strtolower($child->localName), self::ELEMENTS, true)) {
                    $this->clean($child);
                } else {
                    $element->removeChild($child);
                }
            } elseif ($child->nodeType === XML_PI_NODE || $child->nodeType === XML_ENTITY_REF_NODE) {
                $element->removeChild($child);
            }
        }

        if (strtolower($element->localName) === 'style' && ! $this->isSafeStyle((string) $element->textContent)) {
            $element->parentNode?->removeChild($element);
        }
    }

    private function keepsAttribute(DOMElement $element, DOMAttr $attribute): bool
    {
        $name = strtolower($attribute->localName);
        $value = preg_replace('/[\x00-\x20]+/', '', (string) $attribute->value) ?? '';

        if (str_starts_with($name, 'on')) {
            return false;
        }

        if ($name === 'href') {
            return str_starts_with($value, '#')
                || (strtolower($element->localName) === 'image' && preg_match(self::RASTER_DATA_URI, (string) $attribute->value) === 1);
        }

        if ($name === 'style') {
            return $this->isSafeStyle((string) $attribute->value);
        }

        return stripos($value, 'javascript:') === false;
    }

    /**
     * CSS may only reference fragments of this document: no @import, and no
     * url() pointing elsewhere.
     */
    private function isSafeStyle(string $css): bool
    {
        if (stripos($css, '@import') !== false || stripos($css, 'expression(') !== false) {
            return false;
        }

        return preg_match('/url\(\s*[\'"]?\s*(?!#)/i', $css) !== 1;
    }
}
