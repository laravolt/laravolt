<?php

use Laravolt\SemanticForm\Elements\Password;

class PasswordTest extends \PHPUnit\Framework\TestCase
{
    public function test_can_render_basic_text()
    {
        $text = new Password('password');

        $expected = '<input type="password" name="password">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }
}
