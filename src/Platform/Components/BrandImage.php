<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Laravolt\Platform\Services\SvgSanitizer;

class BrandImage extends Component
{
    private const FALLBACK = 'laravolt/img/default/logo.png';

    /** @var array<string, array{svg: string, until: int}> */
    private static array $svgs = [];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        $brandImage = config('laravolt.ui.brand_image');
        $isSvg = false;

        if (Str::of($brandImage)->startsWith('<svg')) {
            $isSvg = true;
            $brandImage = $this->remember('brand_image_inline_'.hash('sha256', $brandImage), fn () => (string) app(SvgSanitizer::class)->sanitize($brandImage));
        } elseif ($brandImage && Str::of($brandImage)->endsWith('.svg')) {
            $isSvg = true;
            $source = $brandImage;
            $brandImage = $this->remember('brand_image_'.hash('sha256', $source), fn () => (string) app(SvgSanitizer::class)->sanitize((string) $this->load($source)));
        }

        // An SVG that could not be read or did not survive sanitizing ('').
        if ($isSvg && $brandImage === '') {
            $isSvg = false;
            $brandImage = null;
        }

        if (! $brandImage) {
            $brandImage = self::FALLBACK;
        }

        return view('laravolt::components.brand-image', compact('brandImage', 'isSvg'));
    }

    /**
     * Every page shows the logo: keep it in memory for 60 seconds instead of
     * reading the cache store on each render. Failures are cached as '' so a
     * broken URL is not fetched again on every request.
     */
    private function remember(string $cacheKey, callable $resolve): string
    {
        $memo = self::$svgs[$cacheKey] ?? null;
        if ($memo !== null && $memo['until'] > time()) {
            return $memo['svg'];
        }

        $svg = (string) Cache::remember($cacheKey, 60, $resolve);
        self::$svgs[$cacheKey] = ['svg' => $svg, 'until' => time() + 60];

        return $svg;
    }

    private function load(string $source): ?string
    {
        if (Str::of($source)->startsWith(['http://', 'https://'])) {
            try {
                $response = Http::timeout(10)->get($source);
            } catch (Exception $e) {
                Log::warning("Failed to fetch brand image from URL: {$source}", ['error' => $e->getMessage()]);

                return null;
            }

            // The sanitizer only lets an <svg> document through, so another kind of
            // response (an internal page, JSON) never reaches the layout.
            if (! $response->successful() || strlen($response->body()) > SvgSanitizer::MAX_BYTES) {
                Log::warning("Failed to fetch brand image from URL: {$source}", ['status' => $response->status()]);

                return null;
            }

            return $response->body();
        }

        // A path under public/ (symlinks such as public/storage are fine), never above it.
        $relative = ltrim($source, '/');
        if (in_array('..', preg_split('#[/\\\\]+#', $relative) ?: [], true)) {
            return null;
        }

        $path = public_path($relative);
        if (! is_file($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        return $contents === false ? null : $contents;
    }
}
