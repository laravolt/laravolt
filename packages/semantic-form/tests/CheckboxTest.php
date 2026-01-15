<?php

declare(strict_types=1);

use Laravolt\SemanticForm\Elements\Checkbox;

class CheckboxTest extends PHPUnit\Framework\TestCase
{
    public function test_render_basic_checkbox()
    {
        $checkbox = new Checkbox('terms');
        $expected = '<input type="checkbox" name="terms" value="1">';
        $result = $checkbox->render();

        $this->assertEquals($expected, $result);

        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1">';
        $result = $checkbox->render();

        $this->assertEquals($expected, $result);
    }

    public function test_render_basic_checkbox_with_value()
    {
        $checkbox = new Checkbox('terms');
        $expected = '<input type="checkbox" name="terms" value="agree">';
        $result = $checkbox->value('agree')->render();

        $this->assertEquals($expected, $result);

        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="true">';
        $result = $checkbox->value('true')->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_check_checkbox()
    {
        $checkbox = new Checkbox('terms');
        $expected = '<input type="checkbox" name="terms" value="1" checked="checked">';
        $result = $checkbox->check()->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_uncheck_checkbox()
    {
        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1">';
        $result = $checkbox->check()->uncheck()->render();

        $this->assertEquals($expected, $result);
    }

    public function test_default_to_checked()
    {
        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1" checked="checked">';
        $result = $checkbox->defaultToChecked()->render();

        $this->assertEquals($expected, $result);

        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1">';
        $result = $checkbox->defaultToChecked()->uncheck()->render();

        $this->assertEquals($expected, $result);

        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1">';
        $result = $checkbox->uncheck()->defaultToChecked()->render();

        $this->assertEquals($expected, $result);
    }

    public function test_default_to_unchecked()
    {
        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1">';
        $result = $checkbox->defaultToUnchecked()->render();

        $this->assertEquals($expected, $result);

        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1" checked="checked">';
        $result = $checkbox->defaultToUnchecked()->check()->render();

        $this->assertEquals($expected, $result);

        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1" checked="checked">';
        $result = $checkbox->check()->defaultToUnchecked()->render();

        $this->assertEquals($expected, $result);
    }

    public function test_default_checked_state()
    {
        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1" checked="checked">';
        $result = $checkbox->defaultCheckedState(true)->render();

        $this->assertEquals($expected, $result);

        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1">';
        $result = $checkbox->defaultCheckedState(false)->render();

        $this->assertEquals($expected, $result);

        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1">';
        $result = $checkbox->uncheck()->defaultCheckedState(true)->render();

        $this->assertEquals($expected, $result);

        $checkbox = new Checkbox('above_18');
        $expected = '<input type="checkbox" name="above_18" value="1" checked="checked">';
        $result = $checkbox->check()->defaultCheckedState(false)->render();

        $this->assertEquals($expected, $result);
    }
}
