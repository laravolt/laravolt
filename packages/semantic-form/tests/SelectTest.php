<?php

use Illuminate\Support\Facades\Lang;
use Laravolt\SemanticForm\SemanticForm;
use AdamWathan\Form\FormBuilder;

class SelectTest extends PHPUnit_Framework_TestCase
{
    private $form;
    private $builder;

    protected $options = ['ch' => 'China', 'id' => 'Indonesia'];

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
        $expected = '<div class="field"><label for="country">Country</label><select name="country" id="country"><option value="ch">China</option><option value="id">Indonesia</option></select></div>';
        $result = $this->form->select('country', $this->options)->render();
        $this->assertEquals($expected, $result);
    }

    public function testWithValue()
    {
        $expected = '<div class="field"><label for="country">Country</label><select name="country" id="country"><option value="ch">China</option><option value="id" selected>Indonesia</option></select></div>';
        $result = $this->form->select('country', $this->options)->select('id')->render();
        $this->assertEquals($expected, $result);
    }

}
