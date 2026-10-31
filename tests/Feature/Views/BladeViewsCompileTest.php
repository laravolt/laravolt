<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Views;

use Laravolt\Tests\FeatureTest;
use ParseError;
use PhpToken;
use Symfony\Component\Finder\Finder;

class BladeViewsCompileTest extends FeatureTest
{
    public function test_every_package_view_compiles_to_valid_php(): void
    {
        $compiler = app('blade.compiler');
        $broken = [];

        $views = Finder::create()->files()->name('*.blade.php')
            ->in([__DIR__.'/../../../resources/views', __DIR__.'/../../../packages']);

        foreach ($views as $view) {
            try {
                PhpToken::tokenize($compiler->compileString($view->getContents()), TOKEN_PARSE);
            } catch (ParseError $e) {
                $broken[] = $view->getRelativePathname().':'.$e->getLine().' '.$e->getMessage();
            }
        }

        $this->assertSame([], $broken);
    }
}
