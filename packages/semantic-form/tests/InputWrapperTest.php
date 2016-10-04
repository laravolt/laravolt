<?php

use Laravolt\SemanticForm\Elements\InputWrapper;

class InputWrapperTest extends PHPUnit_Framework_TestCase
{
	public function testTextCanBeCreated()
	{
		new InputWrapper();
	}

	public function testCanRenderBasicInput()
	{
        $input = new InputWrapper();

        $expected = '<div class="ui input"><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
	}

	public function testCanRenderInputWithIcon()
	{
        $input = new InputWrapper();
        $input->prependIcon('users');

        $expected = '<div class="ui input left icon"><i class="icon users"></i><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper();
        $input->appendIcon('users');

        $expected = '<div class="ui input icon"><input type="text" name=""><i class="icon users"></i></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        // input cannot have both left and right icon
        $input = new InputWrapper();
        $input->prependIcon('users')->appendIcon('home');

        $expected = '<div class="ui input icon"><input type="text" name=""><i class="icon home"></i></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper();
        $input->appendIcon('home')->prependIcon('users');

        $expected = '<div class="ui input left icon"><i class="icon users"></i><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
	}

    public function testCanRenderInputWithLabel()
    {
        $input = new InputWrapper();
        $input->prependLabel('http://');

        $expected = '<div class="ui input labeled"><div class="ui label">http://</div><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper();
        $input->prependLabel('http://', 'basic');

        $expected = '<div class="ui input labeled"><div class="ui label basic">http://</div><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper();
        $input->appendLabel('kg');

        $expected = '<div class="ui input right labeled"><input type="text" name=""><div class="ui label">kg</div></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper();
        $input->prependLabel('http://')->appendLabel('kg');

        $expected = '<div class="ui input right labeled"><div class="ui label">http://</div><input type="text" name=""><div class="ui label">kg</div></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper();
        $input->appendLabel('kg')->prependLabel('http://');

        $expected = '<div class="ui input right labeled"><div class="ui label">http://</div><input type="text" name=""><div class="ui label">kg</div></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

    }
}
