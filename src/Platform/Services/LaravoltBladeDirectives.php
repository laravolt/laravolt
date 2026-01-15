<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

class LaravoltBladeDirectives
{
    public static function scripts($expression)
    {
        return <<<'HTML'
          <!-- Required plugins -->
          <script src="/laravolt/assets/vendor/preline/dist/index.js?v=3.0.1"></script>
          <script src="/laravolt/assets/vendor/preline/dist/overlay.js?v=3.0.1"></script>
          <!-- Clipboard -->
          <script src="/laravolt/assets/vendor/clipboard/dist/clipboard.min.js"></script>
          <script src="/laravolt/assets/js/hs-copy-clipboard-helper.js"></script>
          <!-- Apexcharts -->
          <script src="/laravolt/assets/vendor/lodash/lodash.min.js"></script>
          <script src="/laravolt/assets/vendor/apexcharts/dist/apexcharts.min.js"></script>
          <script src="/laravolt/assets/vendor/preline/dist/helper-apexcharts.js"></script>
          <script src="/laravolt/assets/vendor/basictable/basictable.min.js"></script>
          <!-- JS INITIALIZATIONS -->
          <script>
            document.addEventListener("DOMContentLoaded", () => {
              const csrfMeta = document.querySelector('meta[name="csrf-token"]')?.content || "";
            });
          </script>

          <!-- Vendor -->
          <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        HTML;
    }

    public static function basictable($expression)
    {
        // Parse the expression to get selector and options
        $params = static::parseExpression($expression);

        $selector = $params[0] ?? '.basictable';
        $options = isset($params[1]) ? $params[1] : [];

        // Convert options array to JavaScript object
        $jsOptions = static::arrayToJsObject($options);

        return <<<HTML
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              new basictable('{$selector}', {$jsOptions});
            });
          </script>
        HTML;
    }

    public static function basictableResponsive($expression)
    {
        $params = static::parseExpression($expression);
        $selector = $params[0] ?? '.basictable';
        $options = isset($params[1]) ? $params[1] : [];

        $defaultOptions = [
            'tableWrap' => true,
            'cardStyle' => 'default',
            'forceResponsive' => true,
        ];

        $mergedOptions = array_merge($defaultOptions, $options);
        $jsOptions = static::arrayToJsObject($mergedOptions);

        return <<<HTML
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              new basictable('{$selector}', {$jsOptions});
            });
          </script>
        HTML;
    }

    public static function basictableCompact($expression)
    {
        $params = static::parseExpression($expression);
        $selector = $params[0] ?? '.basictable';
        $options = isset($params[1]) ? $params[1] : [];

        $defaultOptions = [
            'tableWrap' => true,
            'cardStyle' => 'compact',
            'forceResponsive' => true,
        ];

        $mergedOptions = array_merge($defaultOptions, $options);
        $jsOptions = static::arrayToJsObject($mergedOptions);

        return <<<HTML
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              new basictable('{$selector}', {$jsOptions});
            });
          </script>
        HTML;
    }

    public static function basictableInline($expression)
    {
        $params = static::parseExpression($expression);
        $selector = $params[0] ?? '.basictable';
        $options = isset($params[1]) ? $params[1] : [];

        $defaultOptions = [
            'tableWrap' => true,
            'cardStyle' => 'inline',
            'forceResponsive' => true,
            'inlineSeparator' => 'none',
        ];

        $mergedOptions = array_merge($defaultOptions, $options);
        $jsOptions = static::arrayToJsObject($mergedOptions);

        return <<<HTML
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              new basictable('{$selector}', {$jsOptions});
            });
          </script>
        HTML;
    }

    public static function basictableScrollable($expression)
    {
        $params = static::parseExpression($expression);
        $selector = $params[0] ?? '.basictable';
        $maxHeight = $params[1] ?? '300px';
        $options = isset($params[2]) ? $params[2] : [];

        $defaultOptions = [
            'tableWrap' => true,
            'scrollable' => true,
            'maxHeight' => $maxHeight,
            'forceResponsive' => true,
        ];

        $mergedOptions = array_merge($defaultOptions, $options);
        $jsOptions = static::arrayToJsObject($mergedOptions);

        return <<<HTML
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              new basictable('{$selector}', {$jsOptions});
            });
          </script>
        HTML;
    }

    public static function styles($expression)
    {
        $accent = config('laravolt.ui.colors.'.config('laravolt.ui.color'), '#3b82f6');

        return <<<HTML
          <!-- Vendor -->
          <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

          <!-- Font -->
          <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

          <!-- CSS HS -->
          <link rel="stylesheet" href="/laravolt/assets/css/main.min.css?v=3.0.1">
          <link rel="stylesheet" href="/laravolt/assets/vendor/basictable/basictable.min.css">

          <!-- Theme Check and Update -->
          <script>
            const html = document.querySelector('html');
            const isLightOrAuto = localStorage.getItem('hs_theme') === 'light' || (localStorage.getItem('hs_theme') === 'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches);
            const isDarkOrAuto = localStorage.getItem('hs_theme') === 'dark' || (localStorage.getItem('hs_theme') === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isLightOrAuto && html.classList.contains('dark')) html.classList.remove('dark');
            else if (isDarkOrAuto && html.classList.contains('light')) html.classList.remove('light');
            else if (isDarkOrAuto && !html.classList.contains('dark')) html.classList.add('dark');
            else if (isLightOrAuto && !html.classList.contains('light')) html.classList.add('light');
          </script>

          <link rel="stylesheet" href="/laravolt/assets/vendor/apexcharts/dist/apexcharts.css">
          <style type="text/css">
            :root { --accent: {$accent}; --accent-foreground: #ffffff; }
            .btn-accent { background-color: var(--accent) !important; color: var(--accent-foreground) !important; }
            .btn-accent:hover, .btn-accent:focus { filter: brightness(0.95); }
            .btn-accent-soft { color: var(--accent) !important; background-color: color-mix(in srgb, var(--accent) 12%, transparent) !important; }
            .btn-accent-soft:hover, .btn-accent-soft:focus { background-color: color-mix(in srgb, var(--accent) 18%, transparent) !important; }
            .link-accent { color: var(--accent) !important; }
            .link-accent:hover, .link-accent:focus { text-decoration: underline; }
            input[type="checkbox"], input[type="radio"] { accent-color: var(--accent); }
            input:focus, select:focus, textarea:focus {
              border-color: var(--accent) !important;
              box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 20%, transparent) !important;
            }
            .apexcharts-tooltip.apexcharts-theme-light
            {
              background-color: transparent !important;
              border: none !important;
              box-shadow: none !important;
            }
            .toastify {
              box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
              padding: 0px !important;
              border-radius: 1rem !important;
            }
            @media (max-width: 568px) {
              table.basictable {
                border: unset
              }
              .bt-card-inline table.bt tbody td .bt-content > span {
                color: #9ca3af !important;
              }
            }
          </style>
        HTML;
    }

    protected static function parseExpression($expression)
    {
        // Remove parentheses and trim
        $expression = mb_trim($expression, '()');

        if (empty($expression)) {
            return [];
        }

        // Simple parsing - split by comma and handle arrays
        $parts = [];
        $current = '';
        $bracketCount = 0;
        $quoteChar = null;

        for ($i = 0; $i < mb_strlen($expression); $i++) {
            $char = $expression[$i];

            if ($quoteChar !== null) {
                if ($char === $quoteChar) {
                    $quoteChar = null;
                }
                $current .= $char;
            } elseif ($char === '"' || $char === "'") {
                $quoteChar = $char;
                $current .= $char;
            } elseif ($char === '[') {
                $bracketCount++;
                $current .= $char;
            } elseif ($char === ']') {
                $bracketCount--;
                $current .= $char;
            } elseif ($char === ',' && $bracketCount === 0) {
                $parts[] = mb_trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if (! empty($current)) {
            $parts[] = mb_trim($current);
        }

        // Convert string representations to actual values
        foreach ($parts as &$part) {
            $part = static::parseValue($part);
        }

        return $parts;
    }

    protected static function parseValue($value)
    {
        $value = mb_trim($value);

        // Handle arrays
        if (mb_strpos($value, '[') === 0 && mb_strrpos($value, ']') === mb_strlen($value) - 1) {
            $arrayContent = mb_trim($value, '[]');
            if (empty($arrayContent)) {
                return [];
            }

            $items = explode(',', $arrayContent);
            $parsedItems = [];

            foreach ($items as $item) {
                $parsedItems[] = static::parseValue(mb_trim($item));
            }

            return $parsedItems;
        }

        // Handle strings
        if ((mb_strpos($value, '"') === 0 && mb_strrpos($value, '"') === mb_strlen($value) - 1) ||
            (mb_strpos($value, "'") === 0 && mb_strrpos($value, "'") === mb_strlen($value) - 1)) {
            return mb_trim($value, '"\'');
        }

        // Handle booleans
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }

        // Handle null
        if ($value === 'null') {
            return null;
        }

        // Handle numbers
        if (is_numeric($value)) {
            return mb_strpos($value, '.') !== false ? (float) $value : (int) $value;
        }

        // Default to string
        return $value;
    }

    protected static function arrayToJsObject(array $array): string
    {
        $items = [];

        foreach ($array as $key => $value) {
            if ($value === null) {
                $items[] = "'{$key}': null";
            } elseif (is_bool($value)) {
                $items[] = "'{$key}': ".($value ? 'true' : 'false');
            } elseif (is_numeric($value)) {
                $items[] = "'{$key}': {$value}";
            } elseif (is_string($value)) {
                $items[] = "'{$key}': '{$value}'";
            } elseif (is_array($value)) {
                $items[] = "'{$key}': ".static::arrayToJsObject($value);
            }
        }

        return '{'.implode(', ', $items).'}';
    }
}
