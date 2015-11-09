<?php

use Illuminate\Support\Facades\Lang;
use Laravolt\SemanticForm\SemanticForm;
use AdamWathan\Form\FormBuilder;

class InputSubmitTest extends PHPUnit_Framework_TestCase
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
        $expected = '<button type="submit" class="ui button">Action</button>';
        $result = $this->form->submit('Action')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithName()
    {
        $expected = '<button type="submit" name="register" class="ui button">Action</button>';
        $result = $this->form->submit('Action', 'register')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithNameAndClass()
    {
        $expected = '<button type="submit" name="register" class="ui button primary">Action</button>';
        $result = $this->form->submit('Action', 'register', 'primary')->render();
        $this->assertEquals($expected, $result);
    }

}
