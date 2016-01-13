<?php

use Laravolt\SemanticForm\Elements\Password;

class PasswordTest extends PHPUnit_Framework_TestCase
{
	public function testPasswordCanBeCreated()
	{
		$password = new Password('password');
	}
}
