<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Ui;

use Illuminate\Support\Facades\Blade;
use Laravolt\Tests\FeatureTest;

class ThemeTest extends FeatureTest
{
    public function test_the_classic_look_has_no_theme_attribute(): void
    {
        config(['laravolt.ui.theme' => null]);

        $this->assertStringNotContainsString('data-theme', $this->renderLayout());
    }

    public function test_an_empty_setting_keeps_the_classic_look(): void
    {
        config(['laravolt.ui.theme' => '']);

        $this->assertStringNotContainsString('data-theme', $this->renderLayout());
    }

    public function test_a_theme_is_set_on_the_html_element(): void
    {
        config(['laravolt.ui.theme' => 'workbench']);

        $this->assertMatchesRegularExpression('/<html[^>]*data-theme="theme-workbench"/', $this->renderLayout());
    }

    public function test_the_theme_name_is_escaped(): void
    {
        config(['laravolt.ui.theme' => 'x" onload="alert(1)']);

        $html = $this->renderLayout();

        $this->assertStringNotContainsString('onload="alert(1)"', $html);
        $this->assertStringContainsString('data-theme="theme-x&quot; onload=&quot;alert(1)"', $html);
    }

    public function test_the_settings_form_offers_the_themes(): void
    {
        $field = collect(config('laravolt.platform.settings'))->firstWhere('name', 'theme');

        $this->assertSame(['', 'workbench'], array_keys($field['options']));
    }

    private function renderLayout(): string
    {
        return Blade::render('<x-volt-base>content</x-volt-base>');
    }
}
