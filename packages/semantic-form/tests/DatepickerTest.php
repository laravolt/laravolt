<?php

use Laravolt\SemanticForm\Elements\Datepicker;

class DatepickerTest extends \PHPUnit\Framework\TestCase
{
    public function testCanRenderBasicDatepicker()
    {
        $text = new Datepicker('birthdate');

        $expected = '<input type="text" name="birthdate">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function testCanSetDatepickerAsReadonly()
    {
        $text = (new Datepicker('birthdate'))->readonly();

        $expected = '<input type="text" name="birthdate" readonly="readonly">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }
}
