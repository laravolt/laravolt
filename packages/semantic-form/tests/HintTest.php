<?php

declare(strict_types=1);

use Laravolt\SemanticForm\Elements\Hint;
use Laravolt\SemanticForm\Elements\Text;

class HintTest extends PHPUnit\Framework\TestCase
{
    public function test_text_can_have_hint()
    {
        $text = (new Text('email'))->hint('Hint');

        $expected = '<input type="text" name="email"><div class="hint">Hint</div>';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function test_text_can_have_hint_with_custom_class()
    {
        $text = (new Text('email'))->hint('Hint', 'custom-hint-class');

        $expected = '<input type="text" name="email"><div class="custom-hint-class">Hint</div>';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function test_text_can_have_label_and_hint()
    {
        $text = (new Text('email'))->hint('Hint')->label('Email');

        $expected = '<div class="field"><label>Email</label><input type="text" name="email"><div class="hint">Hint</div></div>';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_override_class()
    {
        Hint::$defaultClass = 'custom-hint-class';
        $text = (new Text('email'))->hint('Hint')->label('Email');

        $expected = '<div class="field"><label>Email</label><input type="text" name="email"><div class="custom-hint-class">Hint</div></div>';
        $result = $text->render();
        $this->assertEquals($expected, $result);

        Hint::$defaultClass = 'hint';
    }
}
