<?php

use Illuminate\Support\Facades\Lang;
use Laravolt\SemanticForm\SemanticForm;
use AdamWathan\Form\FormBuilder;

class InputTextTest extends PHPUnit_Framework_TestCase
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
        $expected = '<div class="field"><label for="email">Email</label><input type="text" name="email" id="email"></div>';
        $result = $this->form->text('email')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithValue()
    {
        $expected = '<div class="field"><label for="email">Email</label><input type="text" name="email" id="email" value="example@example.com"></div>';
        $result = $this->form->text('email')->value('example@example.com')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithError()
    {
        $errorStore = Mockery::mock(\AdamWathan\Form\ErrorStore\IlluminateErrorStore::class);
        $errorStore->shouldReceive('hasError')->andReturn(true);
        $errorStore->shouldReceive('getError')->andReturn('Email is required.');

        $this->builder->setErrorStore($errorStore);

        $expected = '<div class="field error"><label for="email">Email</label><input type="text" name="email" id="email"></div>';
        $result = $this->form->text('email')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithOldInput()
    {
        $oldInput = Mockery::mock(\AdamWathan\Form\OldInput\IlluminateOldInputProvider::class);
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->andReturn('example@example.com');

        $this->builder->setOldInputProvider($oldInput);

        $expected = '<div class="field"><label for="email">Email</label><input type="text" name="email" value="example@example.com" id="email"></div>';
        $result = $this->form->text('email', 'Email', 'default@email.com')->render();
        $this->assertEquals($expected, $result);
    }

    public function testpWithOldInputAndDefaultValue()
    {
        $oldInput = Mockery::mock(\AdamWathan\Form\OldInput\IlluminateOldInputProvider::class);
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->andReturn('old@email.com');

        $this->builder->setOldInputProvider($oldInput);

        $expected = '<div class="field"><label for="email">Email</label><input type="text" name="email" value="old@email.com" id="email"></div>';
        $result = $this->form->text('email')->defaultValue('default@email.com')->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithDefaultValue()
    {
    	$expected = '<div class="field"><label for="email">Email</label><input type="text" name="email" id="email" value="default@email.com"></div>';
    	$result = $this->form->text('email')->defaultValue('default@email.com')->render();
    	$this->assertEquals($expected, $result);
    }

    public function testWithCustomLabel()
    {
        $expected = '<div class="field"><label for="email">Email Address</label><input type="text" name="email" id="email"></div>';
        $result = $this->form->text('email', 'Email Address')->render();
        $this->assertEquals($expected, $result);
    }

}
