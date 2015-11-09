<?php

use Illuminate\Support\Facades\Lang;
use Laravolt\SemanticForm\SemanticForm;
use AdamWathan\Form\FormBuilder;

class ButtonTest extends PHPUnit_Framework_TestCase
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
        $expected = '<button type="button" class="ui button">Action</button>';
        $result = $this->form->button('Action')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithName()
    {
        $expected = '<button type="button" name="register" class="ui button">Action</button>';
        $result = $this->form->button('Action', 'register')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithNameAndClass()
    {
        $expected = '<button type="button" name="register" class="ui button primary">Action</button>';
        $result = $this->form->button('Action', 'register', 'primary')->render();
        $this->assertEquals($expected, $result);
    }

}
