<?php

use Illuminate\Support\Facades\Lang;
use Laravolt\SemanticForm\SemanticForm;
use AdamWathan\Form\FormBuilder;

class InputTextWithLabelTest extends PHPUnit_Framework_TestCase
{
    private $form;
    private $builder;

    public function setUp()
    {
        $this->builder = new FormBuilder;

        $translator = Mockery::mock('Illuminate\Translation\Translator');
        $translator->shouldReceive('has')->with('forms.email')->andReturn(true);
        $translator->shouldReceive('get')->with('forms.email')->andReturn('Email Address');

        $this->form = new SemanticForm($this->builder, $translator);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testBasic()
    {
        $expected = '<div class="field"><label for="email">Email Address</label><input type="text" name="email" id="email"></div>';
        $result = $this->form->text('email', 'Email Address')->render();
        $this->assertEquals($expected, $result);
    }

    public function testFromTranslation()
    {
        $expected = '<div class="field"><label for="email">Email Address</label><input type="text" name="email" id="email"></div>';
        $result = $this->form->text('email')->render();
        $this->assertEquals($expected, $result);
    }
}
