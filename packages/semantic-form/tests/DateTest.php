<?php

declare(strict_types=1);

use Laravolt\SemanticForm\Elements\Date;

class DateTest extends PHPUnit\Framework\TestCase
{
    public function test_render_date_input()
    {
        $date = new Date('birthday');
        $expected = '<input type="date" name="birthday">';
        $result = $date->render();
        $this->assertEquals($expected, $result);
    }
}
