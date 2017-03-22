<?php

use Laravolt\SemanticForm\Elements\Button;

class ButtonTest extends PHPUnit_Framework_TestCase
{
    public function testButtonCanBeCreated()
    {
        $submit = new Button('Click Me', 'click-me');
    }

    public function testRenderBasicButton()
    {
        $button = new Button('Click Me', 'click-me');
        $expected = '<button type="button" class="ui button" name="click-me">Click Me</button>';
        $result = $button->render();

        $this->assertEquals($expected, $result);
    }

    public function testCanChangeLabel()
    {
        $button = new Button('Button', null);
        $button->label('Click Me');
        $expected = '<button type="button" class="ui button">Click Me</button>';
        $result = $button->render();

        $this->assertEquals($expected, $result);
    }

    public function testCanChangeValue()
    {
        $button = new Button('Button', null);
        $button->value('save');
        $expected = '<button type="button" class="ui button" value="save">Button</button>';
        $result = $button->render();

        $this->assertEquals($expected, $result);
    }
}
