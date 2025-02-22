<?php

use Laravolt\SemanticForm\Elements\Email;

class EmailTest extends \PHPUnit\Framework\TestCase
{
    public function test_render_email_input()
    {
        $email = new Email('email');
        $expected = '<input type="email" name="email">';
        $result = $email->render();
        $this->assertEquals($expected, $result);
    }
}
