<?php

use Laravolt\SemanticForm\Elements\Password;

class PasswordTest extends \PHPUnit\Framework\TestCase
{
    public function testCanRenderBasicText()
    {
        $text = new Password('password');

        $expected = '<input type="password" name="password">';
        $result = $text->render();
        $this->assertEquals($expected, $result);
    }
}
