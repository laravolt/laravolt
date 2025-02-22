<?php

use Laravolt\SemanticForm\Elements\RadioButton;

class RadioButtonTest extends \PHPUnit\Framework\TestCase
{
    public function test_render_basic_radio_button()
    {
        $radio = new RadioButton('terms');
        $expected = '<input type="radio" name="terms" value="terms">';
        $result = $radio->render();

        $this->assertEquals($expected, $result);

        $radio = new RadioButton('above_18');
        $expected = '<input type="radio" name="above_18" value="above_18">';
        $result = $radio->render();

        $this->assertEquals($expected, $result);
    }

    public function test_render_radio_button_with_value()
    {
        $radio = new RadioButton('color', 'green');
        $expected = '<input type="radio" name="color" value="green">';
        $result = $radio->render();

        $this->assertEquals($expected, $result);

        $radio = new RadioButton('color', 'red');
        $expected = '<input type="radio" name="color" value="red">';
        $result = $radio->render();

        $this->assertEquals($expected, $result);
    }

    public function test_default_to_checked()
    {
        $checkbox = new RadioButton('above_18');
        $expected = '<input type="radio" name="above_18" value="above_18" checked="checked">';
        $result = $checkbox->defaultToChecked()->render();

        $this->assertEquals($expected, $result);

        $checkbox = new RadioButton('above_18');
        $expected = '<input type="radio" name="above_18" value="above_18">';
        $result = $checkbox->defaultToChecked()->uncheck()->render();

        $this->assertEquals($expected, $result);

        $checkbox = new RadioButton('above_18');
        $expected = '<input type="radio" name="above_18" value="above_18">';
        $result = $checkbox->uncheck()->defaultToChecked()->render();

        $this->assertEquals($expected, $result);
    }

    public function test_default_to_unchecked()
    {
        $checkbox = new RadioButton('above_18');
        $expected = '<input type="radio" name="above_18" value="above_18">';
        $result = $checkbox->defaultToUnchecked()->render();

        $this->assertEquals($expected, $result);

        $checkbox = new RadioButton('above_18');
        $expected = '<input type="radio" name="above_18" value="above_18" checked="checked">';
        $result = $checkbox->defaultToUnchecked()->check()->render();

        $this->assertEquals($expected, $result);

        $checkbox = new RadioButton('above_18');
        $expected = '<input type="radio" name="above_18" value="above_18" checked="checked">';
        $result = $checkbox->check()->defaultToUnchecked()->render();

        $this->assertEquals($expected, $result);
    }
}
