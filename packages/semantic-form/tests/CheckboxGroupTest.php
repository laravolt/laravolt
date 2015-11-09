<?php

use Illuminate\Support\Facades\Lang;
use Laravolt\SemanticForm\SemanticForm;
use AdamWathan\Form\FormBuilder;

class CheckboxGroupTest extends PHPUnit_Framework_TestCase
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
        $expected = '<div class="fields grouped"><label>Fruit[]</label><div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit[]" value="apple"><label>Apple</label></div></div></div>';
        $result = $this->form->checkboxGroup('fruit[]', ['apple' => 'Apple'])->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithLabel()
    {
        $expected = '<div class="fields grouped"><label>Fruit</label><div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit[]" value="apple"><label>Apple</label></div></div></div>';
        $result = $this->form->checkboxGroup('fruit[]', ['apple' => 'Apple'], null, 'Fruit')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithValue()
    {
        $expected = '<div class="fields grouped"><label>Fruit</label><div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit[]" value="apple" checked="checked"><label>Apple</label></div></div><div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit[]" value="banana"><label>Banana</label></div></div></div>';
        $result = $this->form->checkboxGroup('fruit[]', ['apple' => 'Apple', 'banana' => 'Banana'], 'apple', 'Fruit')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithValueArray()
    {
        $expected = '<div class="fields grouped"><label>Fruit</label><div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit[]" value="apple" checked="checked"><label>Apple</label></div></div><div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit[]" value="banana"><label>Banana</label></div></div></div>';
        $result = $this->form->checkboxGroup('fruit[]', ['apple' => 'Apple', 'banana' => 'Banana'], ['apple'], 'Fruit')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithOldInput()
    {
        $oldInput = Mockery::mock(\AdamWathan\Form\OldInput\IlluminateOldInputProvider::class);
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('fruit')->andReturn(['apple']);

        $this->builder->setOldInputProvider($oldInput);

        $expected = '<div class="fields grouped"><label>Fruit</label><div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit[]" value="apple" checked="checked"><label>Apple</label></div></div><div class="field"><div class="ui checkbox"><input type="checkbox" name="fruit[]" value="banana"><label>Banana</label></div></div></div>';
        $result = $this->form->checkboxGroup('fruit[]', ['apple' => 'Apple', 'banana' => 'Banana'], 'banana', 'Fruit')->render();
        $this->assertEquals($expected, $result);
    }

}
