<?php

use Laravolt\SemanticForm\Elements\Button;

class ButtonTest extends \PHPUnit\Framework\TestCase
{
    public function testRenderBasicButton()
    {
        $button = new Button('Click Me', 'click-me');
        $expected = '<button type="button" class="ui button" themed name="click-me">Click Me</button>';
        $result = $button->render();

        $this->assertEquals($expected, $result);
    }

    public function testCanChangeText()
    {
        $button = new Button('Button', null);
        $button->text('Click Me');
        $expected = '<button type="button" class="ui button" themed>Click Me</button>';
        $result = $button->render();

        $this->assertEquals($expected, $result);
    }

    public function testCanChangeValue()
    {
        $button = new Button('Button', null);
        $button->value('save');
        $expected = '<button type="button" class="ui button" themed value="save">Button</button>';
        $result = $button->render();

        $this->assertEquals($expected, $result);
    }

    public function testCanHaveLabel()
    {
        $button = new Button('Button', null);
        $button->label('Label');

        $expected = '<div class="field"><label>Label</label><button type="button" class="ui button" themed>Button</button></div>';
        $result = $button->render();

        $this->assertEquals($expected, $result);
    }
}
