<?php

declare(strict_types=1);

use Laravolt\SemanticForm\Elements\Datepicker;

class DatepickerTest extends PHPUnit\Framework\TestCase
{
    public function test_can_render_basic_datepicker()
    {
        $text = new Datepicker('birthdate');

        $expected = '<input type="text" name="birthdate">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_set_datepicker_as_readonly()
    {
        $text = (new Datepicker('birthdate'))->readonly();

        $expected = '<input type="text" name="birthdate" readonly="readonly">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }
}
