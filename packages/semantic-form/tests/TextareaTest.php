
<?php

use Illuminate\Support\Facades\Lang;
use Laravolt\SemanticForm\SemanticForm;
use AdamWathan\Form\FormBuilder;

class TextArea extends PHPUnit_Framework_TestCase
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
        $expected = '<div class="field"><label for="note">Note</label><textarea name="note" rows="10" cols="50" id="note"></textarea></div>';
        $result = $this->form->textarea('note')->render();
        $this->assertEquals($expected, $result);
    }

}
