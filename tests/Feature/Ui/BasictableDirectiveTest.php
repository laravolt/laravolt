<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Ui;

use Illuminate\Support\Facades\Blade;
use Laravolt\Tests\FeatureTest;

class BasictableDirectiveTest extends FeatureTest
{
    public function test_inline_cards_use_their_defaults(): void
    {
        $html = Blade::render("@basictableInline('.basictable')");

        $this->assertStringContainsString(
            'new basictable(".basictable", {"tableWrap":true,"cardStyle":"inline","forceResponsive":true,"inlineSeparator":"none"});',
            $html,
        );
    }

    public function test_options_are_passed_as_a_keyed_array(): void
    {
        $html = Blade::render("@basictable('.orders', ['breakpoint' => 768, 'showColon' => false])");

        $this->assertStringContainsString('new basictable(".orders", {"breakpoint":768,"showColon":false});', $html);
    }

    public function test_options_override_the_style_defaults(): void
    {
        $html = Blade::render("@basictableCompact('.orders', ['tableWrap' => false])");

        $this->assertStringContainsString('{"tableWrap":false,"cardStyle":"compact","forceResponsive":true}', $html);
    }

    public function test_arguments_can_be_variables(): void
    {
        $html = Blade::render('@basictableResponsive($selector, $options)', [
            'selector' => '#stock',
            'options' => ['breakpoint' => 640],
        ]);

        $this->assertStringContainsString('new basictable("#stock", {"tableWrap":true,"cardStyle":"default","forceResponsive":true,"breakpoint":640});', $html);
    }

    public function test_the_selector_defaults_to_basictable(): void
    {
        $html = Blade::render('@basictable');

        $this->assertStringContainsString('new basictable(".basictable", {});', $html);
    }

    public function test_scrollable_takes_a_max_height(): void
    {
        $html = Blade::render("@basictableScrollable('.logs', '240px')");

        $this->assertStringContainsString('new basictable(".logs", {"tableWrap":true,"scrollable":true,"maxHeight":"240px","forceResponsive":true});', $html);
    }

    public function test_values_cannot_break_out_of_the_script(): void
    {
        $html = Blade::render('@basictable($selector)', ['selector' => "</script><script>alert('x')"]);

        $this->assertStringNotContainsString('</script><script>', $html);
        $this->assertStringContainsString('</script>', $html);
    }
}
