<?php

use Illuminate\Support\Facades\Lang;
use Laravolt\SemanticForm\SemanticForm;
use AdamWathan\Form\FormBuilder;

class CheckboxTest extends PHPUnit_Framework_TestCase
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
        $expected = '<div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit" value="Apple"><label>Apple</label></div></div>';
        $result = $this->form->checkbox('fruit', 'Apple')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithLabel()
    {
        $expected = '<div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit" value="Apple"><label>Custom Label</label></div></div>';
        $result = $this->form->checkbox('fruit', 'Apple', 'Custom Label')->render();
        $this->assertEquals($expected, $result);
    }

    public function testChecked()
    {
        $expected = '<div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit" value="Apple" checked="checked"><label>Apple</label></div></div>';
        $result = $this->form->checkbox('fruit', 'Apple')->check()->render();
        $this->assertEquals($expected, $result);
    }

    public function testCheckedByVariable()
    {
        $expected = '<div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit" value="Apple" checked="checked"><label>Apple</label></div></div>';
        $result = $this->form->checkbox('fruit', 'Apple')->setChecked(true)->render();
        $this->assertEquals($expected, $result);
    }

    public function testUncheckedByVariable()
    {
        $expected = '<div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit" value="Apple"><label>Apple</label></div></div>';
        $result = $this->form->checkbox('fruit', 'Apple')->setChecked(false)->render();
        $this->assertEquals($expected, $result);
    }

    public function testChaining()
    {
        $expected = '<div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit" value="Apple"><label>Apple</label></div></div>';
        $result = $this->form->checkbox('fruit', 'Apple')->uncheck()->check()->setChecked(false)->render();
        $this->assertEquals($expected, $result);
    }
}
