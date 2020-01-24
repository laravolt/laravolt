<?php

use Laravolt\SemanticForm\Elements\Number;

class NumberTest extends \PHPUnit\Framework\TestCase
{
    public function testCanRenderBasicText()
    {
        $text = new Number('total');

        $expected = '<input type="number" name="total">';
        $result = $text->render();
        $this->assertEquals($expected, $result);

        $text = new Number('sub_total');

        $expected = '<input type="number" name="sub_total">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanRenderWithId()
    {
        $text = new Number('total');
        $text = $text->id('total_field');

        $expected = '<input type="number" name="total" id="total_field">';
        $result = $text->render();
        $this->assertEquals($expected, $result);

        $text = new Number('sub_total');
        $text = $text->id('sub_total_field');

        $expected = '<input type="number" name="sub_total" id="sub_total_field">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanRenderWithValue()
    {
        $text = new Number('total');
        $text = $text->value(10);

        $expected = '<input type="number" name="total" value="10">';
        $result = $text->render();
        $this->assertEquals($expected, $result);

        $text = new Number('sub_total');
        $text = $text->value(30);

        $expected = '<input type="number" name="sub_total" value="30">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanRenderWithClass()
    {
        $text = new Number('total');
        $text = $text->addClass('error');

        $expected = '<input type="number" name="total" class="error">';
        $result = $text->render();
        $this->assertEquals($expected, $result);

        $text = new Number('sub_total');
        $text = $text->addClass('success');

        $expected = '<input type="number" name="sub_total" class="success">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanRenderWithPlaceholder()
    {
        $text = new Number('total');
        $text = $text->placeholder('Insert total');

        $expected = '<input type="number" name="total" placeholder="Insert total">';
        $result = $text->render();
        $this->assertEquals($expected, $result);

        $text = new Number('sub_total');
        $text = $text->placeholder('Insert sub total');

        $expected = '<input type="number" name="sub_total" placeholder="Insert sub total">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanBeCastToString()
    {
        $text = new Number('total');

        $expected = $text->render();
        $result = (string) $text;
        $this->assertEquals($expected, $result);
    }

    public function testRequired()
    {
        $text = new Number('total');

        $expected = '<input type="number" name="total" required="required">';
        $result = $text->required()->render();
        $this->assertEquals($expected, $result);
    }

    public function testAutofocus()
    {
        $text = new Number('');

        $result = $text->autofocus()->render();
        $message = 'autofocus attribute should be set';
        $this->assertStringContainsString('autofocus="autofocus"', $result, $message);
    }

    public function testUnfocus()
    {
        $pattern = 'autofocus="autofocus"';
        $text = new Number('');

        $result = $text->unfocus()->render();
        $message = 'autofocus attribute should not be set';
        $this->assertStringNotContainsString($pattern, $result, $message);

        $text = new Number('');

        $result = $text->autofocus()->unfocus()->render();
        $message = 'autofocus attribute should be removed';
        $this->assertStringNotContainsString($pattern, $result, $message);
    }

    public function testOptional()
    {
        $text = new Number('total');

        $expected = '<input type="number" name="total">';
        $result = $text->optional()->render();
        $this->assertEquals($expected, $result);

        $result = $text->required()->optional()->render();
        $this->assertEquals($expected, $result);
    }

    public function testDisable()
    {
        $text = new Number('total');

        $expected = '<input type="number" name="total" disabled="disabled">';
        $result = $text->disable()->render();
        $this->assertEquals($expected, $result);
    }

    public function testEnable()
    {
        $text = new Number('total');

        $expected = '<input type="number" name="total">';
        $result = $text->enable()->render();
        $this->assertEquals($expected, $result);

        $result = $text->disable()->enable()->render();
        $this->assertEquals($expected, $result);
    }

    public function testDefaultValue()
    {
        $text = new Number('total');

        $expected = '<input type="number" name="total" value="10">';
        $result = $text->defaultValue('10')->render();
        $this->assertEquals($expected, $result);

        $text = new Number('total');

        $expected = '<input type="number" name="total" value="10">';
        $result = $text->value('10')->defaultValue('20')->render();
        $this->assertEquals($expected, $result);

        $text = new Number('total');

        $expected = '<input type="number" name="total" value="10">';
        $result = $text->defaultValue('20')->value('10')->render();
        $this->assertEquals($expected, $result);
    }

    public function testCustomAttribute()
    {
        $text = new Number('total');

        $expected = '<input type="number" name="total" data-sample="20">';
        $result = $text->attribute('data-sample', '20')->render();
        $this->assertEquals($expected, $result);

        $expected = '<input type="number" name="total">';
        $result = $text->clear('data-sample')->render();
        $this->assertEquals($expected, $result);
    }

    public function testDataAttribute()
    {
        $text = new Number('total');

        $expected = '<input type="number" name="total" data-sample="10">';
        $result = $text->data('sample', '10')->render();
        $this->assertEquals($expected, $result);

        $text = new Number('total');

        $expected = '<input type="number" name="total" data-custom="20">';
        $result = $text->data('custom', '20')->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanRemoveClass()
    {
        $text = new Number('total');
        $text = $text->addClass('error');

        $expected = '<input type="number" name="total" class="error">';
        $result = $text->render();
        $this->assertEquals($expected, $result);

        $text = $text->addClass('large');

        $expected = '<input type="number" name="total" class="error large">';
        $result = $text->render();
        $this->assertEquals($expected, $result);

        $text = $text->removeClass('error');

        $expected = '<input type="number" name="total" class="large">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanAddAttributesThroughMagicMethods()
    {
        $text = new Number('total');
        $text = $text->maxlength('5');

        $expected = '<input type="number" name="total" maxlength="5">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanAddAttributesThroughMagicMethodsWithOptionalParameter()
    {
        $text = new Number('cow');
        $text = $text->moo();

        $expected = '<input type="number" name="cow" moo="moo">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanHaveLabel()
    {
        $text = (new Number('total'))->label('Total');

        $expected = '<div class="field"><label>Total</label><input type="number" name="total"></div>';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanSetStep()
    {
        $text = (new Number('total'))->step('0.01');

        $expected = '<input type="number" name="total" step="0.01">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanSetMin()
    {
        $text = (new Number('total'))->min('0');

        $expected = '<input type="number" name="total" min="0">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanSetMax()
    {
        $text = (new Number('total'))->max('10');

        $expected = '<input type="number" name="total" max="10">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }
}
