<?php

use Laravolt\SemanticForm\Elements\Hint;
use Laravolt\SemanticForm\Elements\Text;

class HintTest extends \PHPUnit\Framework\TestCase
{
    public function testTextCanHaveHint()
    {
        $text = (new Text('email'))->hint('Hint');

        $expected = '<input type="text" name="email"><div class="hint">Hint</div>';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testTextCanHaveHintWithCustomClass()
    {
        $text = (new Text('email'))->hint('Hint', 'custom-hint-class');

        $expected = '<input type="text" name="email"><div class="custom-hint-class">Hint</div>';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testTextCanHaveLabelAndHint()
    {
        $text = (new Text('email'))->hint('Hint')->label('Email');

        $expected = '<div class="field"><label>Email</label><input type="text" name="email"><div class="hint">Hint</div></div>';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanOverrideClass()
    {
        Hint::$defaultClass = 'custom-hint-class';
        $text = (new Text('email'))->hint('Hint')->label('Email');

        $expected = '<div class="field"><label>Email</label><input type="text" name="email"><div class="custom-hint-class">Hint</div></div>';
        $result = $text->render();
        $this->assertEquals($expected, $result);

        Hint::$defaultClass = 'hint';
    }
}
