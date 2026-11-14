<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\SemanticForm;

use Laravolt\PrelineForm\Elements\Select as PrelineSelect;
use Laravolt\PrelineForm\Elements\Text as PrelineText;
use Laravolt\SemanticForm\Elements\Redactor;
use Laravolt\SemanticForm\Elements\Select;
use Laravolt\SemanticForm\Elements\Text;
use Laravolt\SemanticForm\Elements\Uploader;
use Laravolt\Tests\PackageTest;

class DisplayValueTest extends PackageTest
{
    public function test_text_value_is_shown_as_text(): void
    {
        $display = (new Text('note'))->value('<b>bold</b> & co')->displayValue();

        $this->assertSame('&lt;b&gt;bold&lt;/b&gt; &amp; co', $display);
    }

    public function test_text_value_keeps_line_breaks(): void
    {
        $display = (new Text('note'))->value("first\nsecond")->displayValue();

        $this->assertSame("first<br />\nsecond", $display);
    }

    public function test_select_option_label_is_shown_as_text(): void
    {
        $display = (new Select('status', ['a' => '<i>Active</i>']))->value('a')->displayValue();

        $this->assertSame('&lt;i&gt;Active&lt;/i&gt;', $display);
    }

    public function test_uploader_lists_files_as_text_inside_the_list(): void
    {
        $display = (new Uploader('files'))->value(['https://example.test/storage/1/report.pdf'])->displayValue();

        $this->assertStringStartsWith("<div class='ui list'><div class='item'>", $display);
        $this->assertStringEndsWith('</a></div></div>', $display);

        $named = (new Uploader('files'))->value(['https://example.test/storage/1/<b>report</b>.pdf'])->displayValue();

        $this->assertStringContainsString('&lt;b&gt;report&lt;/b&gt;.pdf', $named);
        $this->assertStringNotContainsString('<b>', $named);
    }

    public function test_redactor_keeps_formatting_and_drops_event_handlers(): void
    {
        $display = (new Redactor('body'))
            ->value('<p onclick="track()">Hello <strong>there</strong></p>')
            ->displayValue();

        $this->assertStringContainsString('<strong>there</strong>', $display);
        $this->assertStringContainsString('<p>', $display);
        $this->assertStringNotContainsString('onclick', $display);
    }

    public function test_preline_form_values_are_shown_as_text(): void
    {
        $this->assertSame(
            '&lt;b&gt;bold&lt;/b&gt;',
            (new PrelineText('note'))->value('<b>bold</b>')->displayValue()
        );

        $this->assertSame(
            '&lt;i&gt;Active&lt;/i&gt;',
            (new PrelineSelect('status', ['a' => '<i>Active</i>']))->select('a')->displayValue()
        );
    }
}
