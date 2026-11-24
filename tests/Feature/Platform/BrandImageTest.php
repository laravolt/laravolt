<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravolt\Platform\Services\SvgSanitizer;

beforeEach(function (): void {
    Cache::flush();
    // The component keeps rendered logos in a static memo for 60 seconds.
    (new ReflectionProperty(Laravolt\Platform\Components\BrandImage::class, 'svgs'))->setValue(null, []);
});

function sanitizeSvg(string $svg): ?string
{
    return (new SvgSanitizer())->sanitize($svg);
}

it('keeps the drawing of a logo', function (): void {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><style>.a{fill:#4f46e5}</style>'
        .'<defs><linearGradient id="g"><stop offset="0" stop-color="#fff"/></linearGradient></defs>'
        .'<path class="a" d="M0 0h10v10H0z" fill="url(#g)"/><use href="#g"/></svg>';

    $clean = sanitizeSvg($svg);

    expect($clean)->toContain('<path class="a" d="M0 0h10v10H0z" fill="url(#g)"/>')
        ->toContain('<style>.a{fill:#4f46e5}</style>')
        ->toContain('<use href="#g"/>')
        ->toContain('viewBox="0 0 10 10"');
});

it('removes scripts, handlers and outside references', function (string $svg, string $gone): void {
    expect(sanitizeSvg($svg))->not->toBeNull()->not->toContain($gone);
})->with([
    'script' => ['<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>', 'alert'],
    'handler' => ['<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><rect/></svg>', 'onload'],
    'foreignObject' => ['<svg xmlns="http://www.w3.org/2000/svg"><foreignObject><div>x</div></foreignObject></svg>', 'foreignObject'],
    'link' => ['<svg xmlns="http://www.w3.org/2000/svg"><a href="javascript:alert(1)"><rect/></a></svg>', 'javascript'],
    'animate' => ['<svg xmlns="http://www.w3.org/2000/svg"><animate attributeName="href" to="javascript:alert(1)"/></svg>', 'animate'],
    'external use' => ['<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use xlink:href="https://evil.test/x.svg#a"/></svg>', 'evil.test'],
    'external image' => ['<svg xmlns="http://www.w3.org/2000/svg"><image href="https://evil.test/p.png"/></svg>', 'evil.test'],
    'style import' => ['<svg xmlns="http://www.w3.org/2000/svg"><style>@import url(https://evil.test/a.css);</style></svg>', 'evil.test'],
    'style attribute url' => ['<svg xmlns="http://www.w3.org/2000/svg"><rect style="fill:url(https://evil.test/a)"/></svg>', 'evil.test'],
]);

it('keeps embedded raster images', function (): void {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg"><image href="data:image/png;base64,iVBORw0KGgo="/></svg>';

    expect(sanitizeSvg($svg))->toContain('data:image/png;base64,iVBORw0KGgo=');
});

it('refuses documents that are not a plain svg', function (string $input): void {
    expect(sanitizeSvg($input))->toBeNull();
})->with([
    'html' => '<html><body>x</body></html>',
    'entities' => '<!DOCTYPE svg [<!ENTITY a "aaaa">]><svg xmlns="http://www.w3.org/2000/svg">&a;</svg>',
    'not xml' => 'not xml',
    'empty' => '',
]);

it('inlines a fetched logo only after sanitizing it', function (): void {
    Http::fake(['logo.test/*' => Http::response('<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><rect width="1"/></svg>', 200, ['Content-Type' => 'image/svg+xml'])]);
    config(['laravolt.ui.brand_image' => 'https://logo.test/brand.svg']);

    $html = Blade::render('<x-volt-brand-image/>');

    expect($html)->toContain('<rect width="1"/>')->not->toContain('onload');
});

it('falls back to the default logo when a fetched file is not an svg', function (): void {
    Http::fake(['internal.test/*' => Http::response('{"secret":"value"}', 200, ['Content-Type' => 'application/json'])]);
    config(['laravolt.ui.brand_image' => 'http://internal.test/status.svg']);

    $html = Blade::render('<x-volt-brand-image/>');

    expect($html)->not->toContain('secret')->toContain('laravolt/img/default/logo.png');
    Http::assertSentCount(1);

    // The failure is cached: the next render does not fetch again.
    (new ReflectionProperty(Laravolt\Platform\Components\BrandImage::class, 'svgs'))->setValue(null, []);
    Blade::render('<x-volt-brand-image/>');
    Http::assertSentCount(1);
});

it('does not read files above public/', function (): void {
    config(['laravolt.ui.brand_image' => '../tests/fixtures/logo.svg']);

    expect(Blade::render('<x-volt-brand-image/>'))->toContain('laravolt/img/default/logo.png');
});
