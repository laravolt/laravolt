<?php

use Illuminate\Support\Facades\Lang;
use Laravolt\SemanticForm\SemanticForm;
use AdamWathan\Form\FormBuilder;

class PasswordTest extends PHPUnit_Framework_TestCase
{
    private $form;
    private $builder;

    public function setUp()
    {
        $this->builder = new FormBuilder;

        $translator = Mockery::mock('Illuminate\Translation\Translator');
        $translator->shouldReceive('has')->andReturn(false);

        $this->form = new SemanticForm($this->builder, $translator);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testBasic()
    {
        $expected = '<div class="field"><label for="password">Password</label><input type="password" name="password" id="password"></div>';
        $result = $this->form->password('password')->render();
        $this->assertEquals($expected, $result);
    }

}
