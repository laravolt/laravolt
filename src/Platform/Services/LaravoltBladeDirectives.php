<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

class LaravoltBladeDirectives
{
    public static function scripts($expression)
    {
        $v = static::assetVersion();

        return <<<HTML
          <!-- Required plugins -->
          <script src="/laravolt/assets/vendor/preline/dist/index.js?v={$v}"></script>
          <script src="/laravolt/assets/vendor/preline/dist/overlay.js?v={$v}"></script>
          <script src="/laravolt/assets/vendor/preline/dist/datepicker.js?v={$v}"></script>
          <script src="/laravolt/assets/vendor/preline/dist/select.js?v={$v}"></script>
          <!-- Clipboard -->
          <script src="/laravolt/assets/vendor/clipboard/dist/clipboard.min.js"></script>
          <script src="/laravolt/assets/js/hs-copy-clipboard-helper.js"></script>
          <!-- Apexcharts -->
          <script src="/laravolt/assets/vendor/lodash/lodash.min.js"></script>
          <script src="/laravolt/assets/vendor/apexcharts/dist/apexcharts.min.js"></script>
          <script src="/laravolt/assets/vendor/preline/dist/helper-apexcharts.js?v={$v}"></script>
          <script src="/laravolt/assets/vendor/basictable/basictable.min.js?v={$v}"></script>
          <!-- JS INITIALIZATIONS -->
          <script>
            document.addEventListener("DOMContentLoaded", () => {
              const csrfMeta = document.querySelector('meta[name="csrf-token"]')?.content || "";
            });
          </script>

          <!-- Vendor -->
          <script src="/laravolt/assets/vendor/toastify-js/src/toastify.js?v={$v}"></script>
          <!-- form()->uploader(): loads FilePond only on pages that have an uploader -->
          <script src="/laravolt/assets/js/uploader.js?v={$v}" defer></script>
        HTML;
    }

    /**
     * Cache-busting token for the files built by `npm run build:assets`.
     */
    public static function assetVersion(): string
    {
        static $version;

        if ($version === null) {
            $file = dirname(__DIR__, 3).'/resources/assets.version';
            $version = is_file($file) ? trim((string) file_get_contents($file)) : '';
        }

        return $version !== '' ? $version : 'dev';
    }

    /**
     * `@basictable('.selector', ['breakpoint' => 768])`: Basictable with its own defaults.
     * The arguments are PHP, evaluated when the view renders, so variables work too.
     */
    public static function basictable($expression)
    {
        return static::compileBasictable($expression, []);
    }

    /**
     * Cards with each label above its value.
     */
    public static function basictableResponsive($expression)
    {
        return static::compileBasictable($expression, [
            'tableWrap' => true,
            'cardStyle' => 'default',
            'forceResponsive' => true,
        ]);
    }

    /**
     * Like basictableResponsive, with less padding.
     */
    public static function basictableCompact($expression)
    {
        return static::compileBasictable($expression, [
            'tableWrap' => true,
            'cardStyle' => 'compact',
            'forceResponsive' => true,
        ]);
    }

    /**
     * Cards with each label beside its value. The base layout applies this to `.basictable`.
     */
    public static function basictableInline($expression)
    {
        return static::compileBasictable($expression, [
            'tableWrap' => true,
            'cardStyle' => 'inline',
            'forceResponsive' => true,
            'inlineSeparator' => 'none',
        ]);
    }

    /**
     * `@basictableScrollable('.selector', '300px', [...])`: cards in a box that scrolls past the given height.
     */
    public static function basictableScrollable($expression)
    {
        $arguments = mb_trim((string) $expression);

        return '<?php echo \\'.static::class.'::basictableScrollableScript('.$arguments.'); ?>';
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public static function basictableScrollableScript(string $selector = '.basictable', string $maxHeight = '300px', array $options = []): string
    {
        return static::basictableScript([
            'tableWrap' => true,
            'scrollable' => true,
            'maxHeight' => $maxHeight,
            'forceResponsive' => true,
        ], $selector, $options);
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $options
     */
    public static function basictableScript(array $defaults, string $selector = '.basictable', array $options = []): string
    {
        $flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR;
        $selector = json_encode($selector, $flags);
        $options = json_encode((object) array_merge($defaults, $options), $flags);

        return <<<HTML
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              new basictable({$selector}, {$options});
            });
          </script>
        HTML;
    }

    /**
     * @param  array<string, mixed>  $defaults
     */
    protected static function compileBasictable(?string $expression, array $defaults): string
    {
        $arguments = mb_trim((string) $expression);
        $arguments = $arguments === '' ? '' : ', '.$arguments;

        return '<?php echo \\'.static::class.'::basictableScript('.var_export($defaults, true).$arguments.'); ?>';
    }

    public static function styles($expression)
    {
        $accent = config('laravolt.ui.colors.'.config('laravolt.ui.color'), '#3b82f6');
        $v = static::assetVersion();

        return <<<HTML
          <!-- Vendor -->
          <link rel="stylesheet" href="/laravolt/assets/vendor/toastify-js/src/toastify.css?v={$v}">

          <!-- Font -->
          <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

          <!-- CSS HS -->
          <link rel="stylesheet" href="/laravolt/assets/css/main.min.css?v={$v}">
          <link rel="stylesheet" href="/laravolt/assets/vendor/basictable/basictable.min.css?v={$v}">

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
            }
          </style>
        HTML;
    }
}
