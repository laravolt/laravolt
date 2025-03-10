<?php

use Laravolt\SemanticForm\Elements\InputWrapper;

class InputWrapperTest extends \PHPUnit\Framework\TestCase
{
    public function test_can_render_basic_input()
    {
        $input = new InputWrapper;

        $expected = '<div class="ui input"><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_render_with_value()
    {
        $input = new InputWrapper;
        $input->value('foo');

        $expected = '<div class="ui input"><input type="text" name="" value="foo"></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_render_input_with_icon()
    {
        $input = new InputWrapper;
        $input->prependIcon('users');

        $expected = '<div class="ui input left icon"><i class="icon users"></i><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper;
        $input->appendIcon('users');

        $expected = '<div class="ui input icon"><input type="text" name=""><i class="icon users"></i></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        // input cannot have both left and right icon
        $input = new InputWrapper;
        $input->prependIcon('users')->appendIcon('home');

        $expected = '<div class="ui input icon"><input type="text" name=""><i class="icon home"></i></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper;
        $input->appendIcon('home')->prependIcon('users');

        $expected = '<div class="ui input left icon"><i class="icon users"></i><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_render_labeled_input()
    {
        $input = new InputWrapper;
        $input->prependLabel('http://');

        $expected = '<div class="ui input labeled"><div class="ui label">http://</div><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper;
        $input->prependLabel('http://', 'basic');

        $expected = '<div class="ui input labeled"><div class="ui label basic">http://</div><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper;
        $input->appendLabel('kg');

        $expected = '<div class="ui input right labeled"><input type="text" name=""><div class="ui label">kg</div></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper;
        $input->prependLabel('http://')->appendLabel('kg');

        $expected = '<div class="ui input right labeled"><div class="ui label">http://</div><input type="text" name=""><div class="ui label">kg</div></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper;
        $input->appendLabel('kg')->prependLabel('http://');

        $expected = '<div class="ui input right labeled"><div class="ui label">http://</div><input type="text" name=""><div class="ui label">kg</div></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_render_input_with_field_label()
    {
        $input = new InputWrapper;
        $input->prependLabel('http://')->label('Website');

        $expected = '<div class="field"><label>Website</label><div class="ui input labeled"><div class="ui label">http://</div><input type="text" name=""></div></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input = new InputWrapper;
        $input->label('Website')->appendIcon('link');

        $expected = '<div class="field"><label>Website</label><div class="ui input icon"><input type="text" name=""><i class="icon link"></i></div></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_render_input_with_hint()
    {
        $input = new InputWrapper;
        $input->hint('Hint');

        $expected = '<div class="ui input"><input type="text" name=""></div><div class="hint">Hint</div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_override_type()
    {
        $input = new InputWrapper;
        $input->type('password');

        $expected = '<div class="ui input"><input type="password" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_set_placeholder()
    {
        $input = new InputWrapper;
        $input->placeholder('Fullname...');

        $expected = '<div class="ui input"><input type="text" name="" placeholder="Fullname..."></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_focus()
    {
        $input = new InputWrapper;
        $input->autofocus();

        $expected = '<div class="ui input"><input type="text" name="" autofocus="autofocus"></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_unfocus()
    {
        $input = new InputWrapper;
        $input->autofocus()->unfocus();

        $expected = '<div class="ui input"><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_disable()
    {
        $input = new InputWrapper;
        $input->disable();

        $expected = '<div class="ui input"><input type="text" name="" disabled="disabled"></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_disable_with_parameter()
    {
        $input = new InputWrapper;
        $input->disable(false);

        $expected = '<div class="ui input"><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input->disable(true);

        $expected = '<div class="ui input"><input type="text" name="" disabled="disabled"></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_enable()
    {
        $input = new InputWrapper;
        $input->disable()->enable();

        $expected = '<div class="ui input"><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_enable_with_parameter()
    {
        $input = new InputWrapper;
        $input->disable()->enable(false);

        $expected = '<div class="ui input"><input type="text" name="" disabled="disabled"></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);

        $input->disable()->enable(true);

        $expected = '<div class="ui input"><input type="text" name=""></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_required()
    {
        $input = new InputWrapper;
        $input->required()->label('Username');

        $expected = '<div class="field required"><label>Username</label><div class="ui input"><input type="text" name="" required="required"></div></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }

    public function test_has_error()
    {
        $input = new InputWrapper;
        $input->required()->label('Username');

        $expected = '<div class="field required"><label>Username</label><div class="ui input"><input type="text" name="" required="required"></div></div>';
        $result = $input->render();
        $this->assertEquals($expected, $result);
    }
}
