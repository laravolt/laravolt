<?php

use Laravolt\SemanticForm\Elements\Email;

class EmailTest extends \PHPUnit\Framework\TestCase
{
    public function testRenderEmailInput()
    {
        $email = new Email('email');
        $expected = '<input type="email" name="email">';
        $result = $email->render();
        $this->assertEquals($expected, $result);
    }
}
