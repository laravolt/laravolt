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

class BrandImage extends Component
{
    /** @var array<string, array{svg: mixed, until: int}> */
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
        $isSvg = Str::of($brandImage)->startsWith('<svg');

        if (! $brandImage) {
            $brandImage = 'laravolt/img/default/logo.png';
        }

        if (Str::of($brandImage)->endsWith('.svg')) {
            $isSvg = true;
            $cacheKey = 'brand_image_'.hash('sha256', $brandImage);
            // Every page shows the logo: keep it in memory for the same 60
            // seconds instead of reading the cache store on each render.
            $memo = self::$svgs[$cacheKey] ?? null;
            if ($memo !== null && $memo['until'] > time()) {
                $brandImage = $memo['svg'];
            } else {
                $brandImage = Cache::remember($cacheKey, 60, function () use ($brandImage) {
                    // Handle URLs (http/https)
                    if (Str::of($brandImage)->startsWith(['http://', 'https://'])) {
                        try {
                            $response = Http::timeout(10)->get($brandImage);
                            if ($response->successful()) {
                                return $response->body();
                            }

                            // Fallback to default if URL fails
                            return file_get_contents(public_path('laravolt/img/default/logo.png'));
                        } catch (Exception $e) {
                            // Log error and return fallback
                            Log::warning("Failed to fetch brand image from URL: {$brandImage}", ['error' => $e->getMessage()]);

                            return file_get_contents(public_path('laravolt/img/default/logo.png'));
                        }
                    }

                    // Handle absolute paths starting with /
                    // if (Str::of($brandImage)->startsWith('/')) {
                    //     return file_get_contents(public_path($brandImage));
                    // }

                    // Handle relative paths
                    return file_get_contents(public_path($brandImage));
                });

                self::$svgs[$cacheKey] = ['svg' => $brandImage, 'until' => time() + 60];
            }
        }

        return view('laravolt::components.brand-image', compact('brandImage', 'isSvg'));
    }
}
