<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Session;
use Laravolt\SemanticForm\SemanticForm;

class SemanticFormTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $config = [
            'color' => 'blue',
            'colors' => [
                'red' => '#DB2828',
                'orange' => '#F2711C',
                'yellow' => '#FBBD08',
                'olive' => '#B5CC18',
                'green' => '#21BA45',
                'teal' => '#00B5AD',
                'blue' => '#0052CC',
                'violet' => '#6435C9',
                'purple' => '#A333C8',
                'pink' => '#E03997',
                'brown' => '#A5673F',
                'grey' => '#767676',
                'black' => '#1B1C1D',
            ],
        ];

        $this->form = new SemanticForm($config);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_form_open()
    {
        $expected = '<form method="POST" action="" class="ui form">';
        $result = (string) $this->form->open()->withoutToken();
        $this->assertEquals($expected, $result);
    }

    public function test_form_open_and_bind()
    {
        $object = $this->getStubObject();
        $expected = '<form method="POST" action="" class="ui form">';
        $result = (string) $this->form->open(null, $object)->withoutToken();
        $this->assertEquals($expected, $result);
        $this->assertEquals('John', $this->form->getValueFor('first_name'));
    }

    public function test_form_get()
    {
        $expected = '<form method="GET" action="localhost" class="ui form">';
        $result = (string) $this->form->get('localhost');
        $this->assertEquals($expected, $result);
    }

    public function test_form_post()
    {
        $expected = '<form method="POST" action="localhost" class="ui form">';
        $result = (string) $this->form->post('localhost')->withoutToken();
        $this->assertEquals($expected, $result);
    }

    public function test_form_put()
    {
        $expected = '<form method="POST" action="localhost" class="ui form"><input type="hidden" name="_method" value="PUT">';
        $result = (string) $this->form->put('localhost')->withoutToken();
        $this->assertEquals($expected, $result);
    }

    public function test_form_patch()
    {
        $expected = '<form method="POST" action="localhost" class="ui form"><input type="hidden" name="_method" value="PATCH">';
        $result = (string) $this->form->patch('localhost')->withoutToken();
        $this->assertEquals($expected, $result);
    }

    public function test_form_delete()
    {
        $expected = '<form method="POST" action="localhost" class="ui form"><input type="hidden" name="_method" value="DELETE">';
        $result = (string) $this->form->delete('localhost')->withoutToken();
        $this->assertEquals($expected, $result);
    }

    public function test_form_open_with_action()
    {
        $expected = '<form method="POST" action="submit" class="ui form">';
        $result = (string) $this->form->open('submit')->withoutToken();
        $this->assertEquals($expected, $result);
    }

    public function test_can_close_form()
    {
        $expected = '</form>';
        $result = (string) $this->form->close();
        $this->assertEquals($expected, $result);
    }

    public function test_text_box()
    {
        $expected = '<input type="text" name="email">';
        $result = (string) $this->form->text('email');
        $this->assertEquals($expected, $result);

        $expected = '<input type="text" name="first_name">';
        $result = (string) $this->form->text('first_name');
        $this->assertEquals($expected, $result);
    }

    public function test_text_box_with_label()
    {
        $expected = '<div class="field"><label>Email</label><input type="text" name="email"></div>';
        $result = (string) $this->form->text('email')->label('Email');
        $this->assertEquals($expected, $result);
    }

    public function test_password()
    {
        $expected = '<input type="password" name="password">';
        $result = (string) $this->form->password('password');
        $this->assertEquals($expected, $result);

        $expected = '<input type="password" name="password_confirmed">';
        $result = (string) $this->form->password('password_confirmed');
        $this->assertEquals($expected, $result);
    }

    public function test_password_with_label()
    {
        $expected = '<div class="field"><label>Password</label><input type="password" name="password"></div>';
        $result = (string) $this->form->password('password')->label('Password');
        $this->assertEquals($expected, $result);
    }

    public function test_input_color()
    {
        $expected = '<input type="color" name="background">';
        $result = (string) $this->form->color('background');
        $this->assertEquals($expected, $result);
    }

    public function test_checkbox()
    {
        $expected = '<input type="checkbox" name="terms" value="1">';
        $result = (string) $this->form->checkbox('terms');
        $this->assertEquals($expected, $result);

        $expected = '<input type="checkbox" name="terms" value="agree">';
        $result = (string) $this->form->checkbox('terms', 'agree');
        $this->assertEquals($expected, $result);
    }

    public function test_checkbox_checked()
    {
        $expected = '<input type="checkbox" name="terms" value="agree" checked="checked">';
        $result = (string) $this->form->checkbox('terms', 'agree', true);
        $this->assertEquals($expected, $result);
    }

    public function test_checkbox_with_label()
    {
        $expected = '<div class="field"><div class="ui checkbox"><input type="checkbox" name="terms" value="1"><label>Term</label></div></div>';
        $result = (string) $this->form->checkbox('terms')->label('Term');
        $this->assertEquals($expected, $result);
    }

    public function test_radio()
    {
        $expected = '<input type="radio" name="terms" value="terms">';
        $result = (string) $this->form->radio('terms');
        $this->assertEquals($expected, $result);

        $expected = '<input type="radio" name="terms" value="agree">';
        $result = (string) $this->form->radio('terms', 'agree');
        $this->assertEquals($expected, $result);
    }

    public function test_radio_checked()
    {
        $expected = '<input type="radio" name="terms" value="agree" checked="checked">';
        $result = (string) $this->form->radio('terms', 'agree', true);
        $this->assertEquals($expected, $result);
    }

    public function test_radio_with_label()
    {
        $expected = '<div class="field"><div class="ui radio checkbox"><input type="radio" name="terms" value="terms"><label>Term</label></div></div>';
        $result = (string) $this->form->radio('terms')->label('Term');
        $this->assertEquals($expected, $result);
    }

    public function test_radio_group()
    {
        $expected = '<div class="field">';
        $expected .= '<label>Fruit</label>';
        $expected .= '<div class="grouped fields">';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui radio checkbox">';
        $expected .= '<input type="radio" name="fruit" value="orange">';
        $expected .= '<label>Orange</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui radio checkbox">';
        $expected .= '<input type="radio" name="fruit" value="banana">';
        $expected .= '<label>Banana</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';

        $options = ['orange' => 'Orange', 'banana' => 'Banana'];
        $result = (string) $this->form->radioGroup('fruit', $options)->label('Fruit');
        $this->assertEquals($expected, $result);
    }

    public function test_radio_group_with_value()
    {
        $expected = '<div class="field">';
        $expected .= '<label>Fruit</label>';
        $expected .= '<div class="grouped fields">';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui radio checkbox">';
        $expected .= '<input type="radio" name="fruit" value="orange">';
        $expected .= '<label>Orange</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui radio checkbox">';
        $expected .= '<input type="radio" name="fruit" value="banana" checked="checked">';
        $expected .= '<label>Banana</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';

        $options = ['orange' => 'Orange', 'banana' => 'Banana'];
        $result = (string) $this->form->radioGroup('fruit', $options, 'banana')->label('Fruit');
        $this->assertEquals($expected, $result);
    }

    public function test_radio_group_with_value_and_old_input()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('fruit')->andReturn('orange');

        $this->form->setOldInputProvider($oldInput);

        $expected = '<div class="field">';
        $expected .= '<label>Fruit</label>';
        $expected .= '<div class="grouped fields">';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui radio checkbox">';
        $expected .= '<input type="radio" name="fruit" value="orange" checked="checked">';
        $expected .= '<label>Orange</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui radio checkbox">';
        $expected .= '<input type="radio" name="fruit" value="banana">';
        $expected .= '<label>Banana</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';

        $options = ['orange' => 'Orange', 'banana' => 'Banana'];
        $result = (string) $this->form->radioGroup('fruit', $options, 'banana')->label('Fruit');
        $this->assertEquals($expected, $result);
    }

    public function test_radio_group_inline()
    {
        $expected = '<div class="field">';
        $expected .= '<label>Fruit</label>';
        $expected .= '<div class="inline fields">';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui radio checkbox">';
        $expected .= '<input type="radio" name="fruit" value="orange">';
        $expected .= '<label>Orange</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui radio checkbox">';
        $expected .= '<input type="radio" name="fruit" value="banana">';
        $expected .= '<label>Banana</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';

        $options = ['orange' => 'Orange', 'banana' => 'Banana'];
        $result = (string) $this->form->radioGroup('fruit', $options)->inline()->label('Fruit');
        $this->assertEquals($expected, $result);

        $result = (string) $this->form->radioGroup('fruit', $options)->label('Fruit')->inline();
        $this->assertEquals($expected, $result);
    }

    public function test_checkbox_group()
    {
        $expected = '<div class="field">';
        $expected .= '<label>Fruit</label>';
        $expected .= '<div class="grouped fields">';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui checkbox">';
        $expected .= '<input type="checkbox" name="fruit[orange]" value="orange">';
        $expected .= '<label>Orange</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui checkbox">';
        $expected .= '<input type="checkbox" name="fruit[banana]" value="banana">';
        $expected .= '<label>Banana</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';

        $options = ['orange' => 'Orange', 'banana' => 'Banana'];
        $result = (string) $this->form->checkboxGroup('fruit', $options)->label('Fruit');
        $this->assertEquals($expected, $result);
    }

    public function test_checkbox_group_with_value()
    {
        $expected = '<div class="field">';
        $expected .= '<label>Fruit</label>';
        $expected .= '<div class="grouped fields">';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui checkbox">';
        $expected .= '<input type="checkbox" name="fruit[orange]" value="orange">';
        $expected .= '<label>Orange</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui checkbox">';
        $expected .= '<input type="checkbox" name="fruit[banana]" value="banana" checked="checked">';
        $expected .= '<label>Banana</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';

        $options = ['orange' => 'Orange', 'banana' => 'Banana'];
        $result = (string) $this->form->checkboxGroup('fruit', $options, ['banana'])->label('Fruit');
        $this->assertEquals($expected, $result);

        $result = (string) $this->form->checkboxGroup('fruit', $options, 'banana')->label('Fruit');
        $this->assertEquals($expected, $result);
    }

    public function test_checkbox_group_with_value_and_old_input()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('fruit')->andReturn(['orange' => 'orange']);

        $this->form->setOldInputProvider($oldInput);

        $expected = '<div class="field">';
        $expected .= '<label>Fruit</label>';
        $expected .= '<div class="grouped fields">';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui checkbox">';
        $expected .= '<input type="checkbox" name="fruit[orange]" value="orange" checked="checked">';
        $expected .= '<label>Orange</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui checkbox">';
        $expected .= '<input type="checkbox" name="fruit[banana]" value="banana">';
        $expected .= '<label>Banana</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';

        $options = ['orange' => 'Orange', 'banana' => 'Banana'];
        $result = (string) $this->form->checkboxGroup('fruit', $options, ['banana'])->label('Fruit');
        $this->assertEquals($expected, $result);

        $result = (string) $this->form->checkboxGroup('fruit', $options, 'banana')->label('Fruit');
        $this->assertEquals($expected, $result);
    }

    public function test_checkbox_group_inline()
    {
        $expected = '<div class="field">';
        $expected .= '<label>Fruit</label>';
        $expected .= '<div class="inline fields">';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui checkbox">';
        $expected .= '<input type="checkbox" name="fruit[orange]" value="orange">';
        $expected .= '<label>Orange</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= '<div class="ui checkbox">';
        $expected .= '<input type="checkbox" name="fruit[banana]" value="banana">';
        $expected .= '<label>Banana</label>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';

        $options = ['orange' => 'Orange', 'banana' => 'Banana'];
        $result = (string) $this->form->checkboxGroup('fruit', $options)->label('Fruit')->inline();
        $this->assertEquals($expected, $result);
    }

    public function test_submit()
    {
        $expected = '<button type="submit" class="ui button primary" themed name="submit">Sign In</button>';
        $result = (string) $this->form->submit('Sign In', 'submit');
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider buttonProvider
     */
    public function test_button($label, $name, $value, $expected)
    {
        $result = (string) $this->form->button($label, $name)->value($value);
        $this->assertEquals($expected, $result);
    }

    public function buttonProvider()
    {
        return [
            [
                'Click Me', 'click-me', 'save',
                '<button type="button" class="ui button" themed name="click-me" value="save">Click Me</button>',
            ],
            ['Click Me', null, 'save', '<button type="button" class="ui button" themed value="save">Click Me</button>'],
            ['Click Me', null, null, '<button type="button" class="ui button" themed>Click Me</button>'],
        ];
    }

    public function test_select()
    {
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="red">Red</option><option value="blue">Blue</option></select>';
        $result = (string) $this->form->select('color', ['red' => 'Red', 'blue' => 'Blue']);
        $this->assertEquals($expected, $result);

        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple">Granny Smith</option><option value="berry">Blueberry</option></select>';
        $result = (string) $this->form->select('fruit', ['apple' => 'Granny Smith', 'berry' => 'Blueberry']);
        $this->assertEquals($expected, $result);

        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple" selected>Granny Smith</option><option value="berry">Blueberry</option></select>';
        $result = (string) $this->form->select('fruit', ['apple' => 'Granny Smith', 'berry' => 'Blueberry'], 'apple');
        $this->assertEquals($expected, $result);
    }

    public function test_select_with_label()
    {
        $expected = '<div class="field"><label>Color</label><select class="ui dropdown search clearable selection" name="color"><option value="red">Red</option><option value="blue">Blue</option></select></div>';
        $result = (string) $this->form->select('color', ['red' => 'Red', 'blue' => 'Blue'])->label('Color');
        $this->assertEquals($expected, $result);
    }

    public function test_select_can_prepend_option()
    {
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="">First</option><option value="red">Red</option><option value="blue">Blue</option></select>';
        $result = (string) $this->form->select('color', ['red' => 'Red', 'blue' => 'Blue'])->prependOption('', 'First');
        $this->assertEquals($expected, $result);
    }

    public function test_select_can_have_placeholder()
    {
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="">Please Select</option><option value="red">Red</option><option value="blue">Blue</option></select>';
        $result = (string) $this->form->select('color', ['red' => 'Red', 'blue' => 'Blue'])
            ->placeholder('Please Select');
        $this->assertEquals($expected, $result);
    }

    public function test_select_can_have_placeholder_with_default_label()
    {
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="">-- Select --</option><option value="red">Red</option><option value="blue">Blue</option></select>';
        $result = (string) $this->form->select('color', ['red' => 'Red', 'blue' => 'Blue'])->placeholder();
        $this->assertEquals($expected, $result);
    }

    public function test_select_can_append_option()
    {
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="red">Red</option><option value="blue">Blue</option><option value="">Last</option></select>';
        $result = (string) $this->form->select('color', ['red' => 'Red', 'blue' => 'Blue'])->appendOption('', 'Last');
        $this->assertEquals($expected, $result);
    }

    public function test_text_area()
    {
        $expected = '<textarea name="bio" rows="10" cols="50"></textarea>';
        $result = (string) $this->form->textarea('bio');
        $this->assertEquals($expected, $result);

        $expected = '<textarea name="description" rows="10" cols="50"></textarea>';
        $result = (string) $this->form->textarea('description');
        $this->assertEquals($expected, $result);
    }

    public function test_text_area_with_label()
    {
        $expected = '<div class="field"><label>Bio</label><textarea name="bio" rows="10" cols="50"></textarea></div>';
        $result = (string) $this->form->textarea('bio')->label('Bio');
        $this->assertEquals($expected, $result);
    }

    public function test_label()
    {
        $expected = '<label>Email</label>';
        $result = (string) $this->form->label('Email');
        $this->assertEquals($expected, $result);

        $expected = '<label>First Name</label>';
        $result = (string) $this->form->label('First Name');
        $this->assertEquals($expected, $result);
    }

    public function test_render_text_with_old_input()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('title')->andReturn('Hello "quotes"');

        $this->form->setOldInputProvider($oldInput);

        $expected = '<input type="text" name="title" value="Hello &quot;quotes&quot;">';
        $result = (string) $this->form->text('title');
        $this->assertEquals($expected, $result);
    }

    public function test_render_checkbox_with_old_input()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('terms')->andReturn('agree');

        $this->form->setOldInputProvider($oldInput);

        $expected = '<input type="checkbox" name="terms" value="agree" checked="checked">';
        $result = (string) $this->form->checkbox('terms', 'agree');
        $this->assertEquals($expected, $result);
    }

    public function test_render_radio_with_old_input()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('color')->andReturn('green');

        $this->form->setOldInputProvider($oldInput);

        $expected = '<input type="radio" name="color" value="green" checked="checked">';
        $result = (string) $this->form->radio('color', 'green');
        $this->assertEquals($expected, $result);
    }

    public function test_render_select_with_old_input()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('color')->andReturn('blue');

        $this->form->setOldInputProvider($oldInput);

        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="red">Red</option><option value="blue" selected>Blue</option></select>';
        $result = (string) $this->form->select('color', ['red' => 'Red', 'blue' => 'Blue']);
        $this->assertEquals($expected, $result);
    }

    public function test_render_text_area_with_old_input()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('bio')->andReturn('This is my bio');

        $this->form->setOldInputProvider($oldInput);

        $expected = '<textarea name="bio" rows="10" cols="50">This is my bio</textarea>';
        $result = (string) $this->form->textarea('bio');
        $this->assertEquals($expected, $result);
    }

    public function test_rendering_text_area_with_old_input_escapes_dangerous_characters()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('bio')->andReturn('<script>alert("xss!");</script>');

        $this->form->setOldInputProvider($oldInput);

        $expected = '<textarea name="bio" rows="10" cols="50">&lt;script&gt;alert(&quot;xss!&quot;);&lt;/script&gt;</textarea>';
        $result = (string) $this->form->textarea('bio');
        $this->assertEquals($expected, $result);
    }

    public function test_no_error_store_returns_null()
    {
        $expected = '';
        $result = (string) $this->form->getError('email');
        $this->assertEquals($expected, $result);
    }

    public function test_can_check_for_error_message()
    {
        $errorStore = Mockery::mock('Laravolt\SemanticForm\ErrorStore\ErrorStoreInterface');
        $errorStore->shouldReceive('hasError')->with('email')->andReturn(true);

        $this->form->setErrorStore($errorStore);

        $result = $this->form->hasError('email');
        $this->assertTrue($result);

        $errorStore = Mockery::mock('Laravolt\SemanticForm\ErrorStore\ErrorStoreInterface');
        $errorStore->shouldReceive('hasError')->with('email')->andReturn(false);

        $this->form->setErrorStore($errorStore);

        $result = $this->form->hasError('email');
        $this->assertFalse($result);
    }

    public function test_can_retrieve_error_message()
    {
        $errorStore = Mockery::mock('Laravolt\SemanticForm\ErrorStore\ErrorStoreInterface');
        $errorStore->shouldReceive('hasError')->andReturn(true);
        $errorStore->shouldReceive('getError')->with('email')->andReturn('The e-mail address is invalid.');

        $this->form->setErrorStore($errorStore);

        $expected = 'The e-mail address is invalid.';
        $result = $this->form->getError('email');
        $this->assertEquals($expected, $result);
    }

    public function test_input_wrapper_has_error()
    {
        $errorStore = Mockery::mock('Laravolt\SemanticForm\ErrorStore\ErrorStoreInterface');
        $errorStore->shouldReceive('hasError')->andReturn(true);
        $errorStore->shouldReceive('getError')->with('email')->andReturn('The e-mail address is invalid.');

        $this->form->setErrorStore($errorStore);

        $expected = '<div class="ui input error"><input type="text" name="email"></div>';
        $result = $this->form->input('email')->render();
        $this->assertEquals($expected, $result);

        $expected = '<div class="field error"><label>Email</label><div class="ui input error"><input type="text" name="email"></div></div>';
        $result = $this->form->input('email')->label('Email')->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_retrieve_formatted_error_message()
    {
        $errorStore = Mockery::mock('Laravolt\SemanticForm\ErrorStore\ErrorStoreInterface');
        $errorStore->shouldReceive('hasError')->andReturn(true);
        $errorStore->shouldReceive('getError')->with('email')->andReturn('The e-mail address is invalid.');

        $this->form->setErrorStore($errorStore);

        $expected = '<span class="error">The e-mail address is invalid.</span>';
        $result = $this->form->getError('email', '<span class="error">:message</span>');
        $this->assertEquals($expected, $result);
    }

    public function test_formatted_error_message_returns_nothing_if_no_error()
    {
        $errorStore = Mockery::mock('Laravolt\SemanticForm\ErrorStore\ErrorStoreInterface');
        $errorStore->shouldReceive('hasError')->with('email')->andReturn(false);

        $this->form->setErrorStore($errorStore);

        $expected = '';
        $result = $this->form->getError('email', '<span class="error">:message</span>');
        $this->assertEquals($expected, $result);
    }

    public function test_hidden()
    {
        $expected = '<input type="hidden" name="secret">';
        $result = (string) $this->form->hidden('secret');
        $this->assertEquals($expected, $result);

        $expected = '<input type="hidden" name="token">';
        $result = (string) $this->form->hidden('token');
        $this->assertEquals($expected, $result);
    }

    public function test_file()
    {
        $expected = '<input type="file" name="photo">';
        $result = (string) $this->form->file('photo');
        $this->assertEquals($expected, $result);

        $expected = '<input type="file" name="document">';
        $result = (string) $this->form->file('document');
        $this->assertEquals($expected, $result);
    }

    public function test_date()
    {
        $expected = '<input type="date" name="date_of_birth">';
        $result = (string) $this->form->date('date_of_birth');
        $this->assertEquals($expected, $result);

        $expected = '<input type="date" name="start_date">';
        $result = (string) $this->form->date('start_date');
        $this->assertEquals($expected, $result);
    }

    public function test_email()
    {
        $expected = '<input type="email" name="email">';
        $result = (string) $this->form->email('email');
        $this->assertEquals($expected, $result);

        $expected = '<input type="email" name="alternate_email">';
        $result = (string) $this->form->email('alternate_email');
        $this->assertEquals($expected, $result);
    }

    public function test_render_date_with_old_input()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('date_of_birth')->andReturn('1999-04-06');

        $this->form->setOldInputProvider($oldInput);

        $expected = '<input type="date" name="date_of_birth" value="1999-04-06">';
        $result = (string) $this->form->date('date_of_birth');
        $this->assertEquals($expected, $result);
    }

    public function test_render_email_with_old_input()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('email')->andReturn('example@example.com');

        $this->form->setOldInputProvider($oldInput);

        $expected = '<input type="email" name="email" value="example@example.com">';
        $result = (string) $this->form->email('email');
        $this->assertEquals($expected, $result);
    }

    public function test_render_hidden_with_old_input()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('secret')->andReturn('my-secret-string');

        $this->form->setOldInputProvider($oldInput);

        $expected = '<input type="hidden" name="secret" value="my-secret-string">';
        $result = (string) $this->form->hidden('secret');
        $this->assertEquals($expected, $result);
    }

    public function test_token_is_rendered_automatically()
    {
        Session::shouldReceive('token')->once()->andReturn('999');

        $expected = '<form method="POST" action="" class="ui form"><input type="hidden" name="_token" value="999">';
        $result = (string) $this->form->open();
        $this->assertEquals($expected, $result);
    }

    public function test_select_month()
    {
        $expected = '<select class="ui dropdown search clearable selection" name="month"><option value="1">January</option><option value="2">February</option><option value="3">March</option><option value="4">April</option><option value="5">May</option><option value="6">June</option><option value="7">July</option><option value="8">August</option><option value="9">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option></select>';
        $result = (string) $this->form->selectMonth('month');
        $this->assertEquals($expected, $result);
    }

    public function test_select_range()
    {
        $expected = '<select class="ui dropdown search clearable selection" name="age"><option value="1">1</option><option value="2">2</option></select>';
        $result = (string) $this->form->selectRange('age', 1, 2);
        $this->assertEquals($expected, $result);
    }

    public function test_select_date()
    {
        $date = $this->form->selectRange('_birthdate[date]', 1, 31)->addClass('compact');
        $month = $this->form->selectMonth('_birthdate[month]')->addClass('compact');
        $year = $this->form->selectRange('_birthdate[year]', 2001, 2010)->addClass('compact');

        $expected = '<div class="inline fields">';
        $expected .= '<div class="field">';
        $expected .= $date;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $month;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $year;
        $expected .= '</div>';
        $expected .= '</div>';

        $result = (string) $this->form->selectDate('birthdate', 2001, 2010);

        $this->assertEquals($expected, $result);
    }

    public function test_select_date_with_label()
    {
        $date = $this->form->selectRange('_birthdate[date]', 1, 31)->addClass('compact');
        $month = $this->form->selectMonth('_birthdate[month]')->addClass('compact');
        $year = $this->form->selectRange('_birthdate[year]', 2001, 2010)->addClass('compact');

        $expected = '<div class="field">';
        $expected .= '<label>Birthdate</label>';
        $expected .= '<div class="inline fields">';
        $expected .= '<div class="field">';
        $expected .= $date;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $month;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $year;
        $expected .= '</div>';
        $expected .= '</div>';
        $expected .= '</div>';

        $result = (string) $this->form->selectDate('birthdate', 2001, 2010)->label('Birthdate');

        $this->assertEquals($expected, $result);
    }

    public function test_select_date_can_have_value()
    {
        $date = $this->form->selectRange('_birthdate[date]', 1, 31)->addClass('compact')->select(2);
        $month = $this->form->selectMonth('_birthdate[month]')->addClass('compact')->select(3);
        $year = $this->form->selectRange('_birthdate[year]', 2001, 2010)->addClass('compact')->select(2004);

        $expected = '<div class="inline fields">';
        $expected .= '<div class="field">';
        $expected .= $date;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $month;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $year;
        $expected .= '</div>';
        $expected .= '</div>';

        $result = (string) $this->form->selectDate('birthdate', 2001, 2010)->value('2004-3-2');

        $this->assertEquals($expected, $result);
    }

    public function test_select_date_time()
    {
        $date = $this->form->selectRange('_schedule[date]', 1, 31)->addClass('compact');
        $month = $this->form->selectMonth('_schedule[month]')->addClass('compact');
        $year = $this->form->selectRange('_schedule[year]', 2001, 2010)->addClass('compact');

        $timeOptions = [];
        foreach (range(0, 23) as $hour) {
            if (mb_strlen($hour) === 1) {
                $hour = '0'.$hour;
            }
            $key = $val = sprintf('%s:%s', $hour, '00');
            $timeOptions[$key] = $val;

            $key = $val = sprintf('%s:%s', $hour, 30);
            $timeOptions[$key] = $val;
        }
        $time = $this->form->select('_schedule[time]', $timeOptions)->addClass('compact');

        $expected = '<div class="inline fields">';
        $expected .= '<div class="field">';
        $expected .= $date;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $month;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $year;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $time;
        $expected .= '</div>';
        $expected .= '</div>';

        $result = (string) $this->form->selectDateTime('schedule', 2001, 2010, 30);

        $this->assertEquals($expected, $result);
    }

    public function test_select_date_time_can_have_value()
    {
        $date = $this->form->selectRange('_schedule[date]', 1, 31)->addClass('compact')->select(10);
        $month = $this->form->selectMonth('_schedule[month]')->addClass('compact')->select(11);
        $year = $this->form->selectRange('_schedule[year]', 2001, 2010)->addClass('compact')->select(2004);

        $timeOptions = [];
        foreach (range(0, 23) as $hour) {
            if (mb_strlen($hour) === 1) {
                $hour = '0'.$hour;
            }
            $key = $val = sprintf('%s:%s', $hour, '00');
            $timeOptions[$key] = $val;

            $key = $val = sprintf('%s:%s', $hour, 30);
            $timeOptions[$key] = $val;
        }
        $time = $this->form->select('_schedule[time]', $timeOptions)->addClass('compact')->defaultValue('12:00');

        $expected = '<div class="inline fields">';
        $expected .= '<div class="field">';
        $expected .= $date;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $month;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $year;
        $expected .= '</div>';
        $expected .= '<div class="field">';
        $expected .= $time;
        $expected .= '</div>';
        $expected .= '</div>';

        $result = (string) $this->form->selectDateTime('schedule', 2001, 2010, 30)->defaultValue('2004-11-10 12:00:00');

        $this->assertEquals($expected, $result);
    }

    public function test_input_wrapper()
    {
        $result = (string) $this->form->input('search');
        $expected = '<div class="ui input"><input type="text" name="search"></div>';

        $this->assertEquals($expected, $result);

        $result = (string) $this->form->input('search')->appendIcon('search');
        $expected = '<div class="ui input icon"><input type="text" name="search"><i class="icon search"></i></div>';

        $this->assertEquals($expected, $result);

        $result = (string) $this->form->input('search')->placeholder('Search...');
        $expected = '<div class="ui input"><input type="text" name="search" placeholder="Search..."></div>';

        $this->assertEquals($expected, $result);
    }

    public function test_uploader()
    {
        Illuminate\Support\Facades\Route::shouldReceive('has')->once()->andReturn(true);

        $result = (string) $this->form->uploader('avatar');
        $expected = '<input type="file" class="uploader" data-limit="1" data-file-max-size="10000" name="avatar" data-token="abc123" data-fileuploader-listInput="_avatar" data-media-url="/test">';

        $this->assertEquals($expected, $result);
    }

    public function test_uploader_with_custom_limit()
    {
        $result = (string) $this->form->uploader('avatar')->limit(3);
        $expected = '<input type="file" class="uploader" data-limit="3" data-file-max-size="10000" name="avatar" data-token="abc123" data-fileuploader-listInput="_avatar" data-media-url="/test">';

        $this->assertEquals($expected, $result);
    }

    public function test_uploader_with_custom_extensions()
    {
        $result = (string) $this->form->uploader('avatar')->extensions(['jpg', 'png']);
        $expected = '<input type="file" class="uploader" data-limit="1" data-file-max-size="10000" name="avatar" data-token="abc123" data-fileuploader-listInput="_avatar" data-extensions="jpg,png" data-media-url="/test">';

        $this->assertEquals($expected, $result);
    }

    public function test_rupiah()
    {
        $mock1 = Mockery::mock('alias:Laravolt\Asset\AssetFacade');
        $mock1->shouldReceive('group')->andReturnSelf();
        $mock1->shouldReceive('add')->andReturnSelf();

        $result = (string) $this->form->rupiah('price');
        $expected = '<div class="ui input labeled"><div class="ui label">Rp</div><input type="text" name="price" data-role="rupiah"></div>';

        $this->assertEquals($expected, $result);
    }

    public function test_can_bind_object()
    {
        $this->assertTrue(method_exists($this->form, 'bind'));
    }

    public function test_bind_can_be_chained_before_opening_form()
    {
        $object = $this->getStubObject();
        $this->form->bind($object)->open()->horizontal();
        $expected = 'John';
        $result = $this->form->getValueFor('first_name');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_email()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<input type="email" name="email" value="johndoe@example.com">';
        $result = (string) $this->form->email('email');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_text()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<input type="text" name="first_name" value="John">';
        $result = (string) $this->form->text('first_name');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_text_with_integer_zero()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<input type="text" name="number" value="0">';
        $result = (string) $this->form->text('number');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_date()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<input type="date" name="date_of_birth" value="1985-05-06">';
        $result = (string) $this->form->date('date_of_birth');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_select()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<select class="ui dropdown search clearable selection" name="gender"><option value="male" selected>Male</option><option value="female">Female</option></select>';
        $result = (string) $this->form->select('gender', ['male' => 'Male', 'female' => 'Female']);
        $this->assertEquals($expected, $result);
    }

    public function test_select_multiple_with_default_value()
    {
        $expected = '<select class="ui dropdown search multiple tag" multiple="multiple" name="skills[]" data-value="php"><option value="php" selected>PHP</option><option value="java">Java</option></select>';
        $result = (string) $this->form->selectMultiple('skills[]', ['php' => 'PHP', 'java' => 'Java'], ['php']);
        $this->assertEquals($expected, $result);

        $expected = '<select class="ui dropdown search multiple tag" multiple="multiple" name="skills[]" data-value="php,java"><option value="php" selected>PHP</option><option value="java" selected>Java</option></select>';
        $result = (string) $this->form->selectMultiple('skills[]', ['php' => 'PHP', 'java' => 'Java'], ['php', 'java']);
        $this->assertEquals($expected, $result);
    }

    public function test_bind_select_with_multiple_values()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<select class="ui dropdown search clearable selection" name="skills[]" multiple="multiple"><option value="php" selected>PHP</option><option value="java" selected>Java</option></select>';
        $result = (string) $this->form->select('skills', ['php' => 'PHP', 'java' => 'Java'])->multiple();
        $this->assertEquals($expected, $result);
    }

    public function test_bind_hidden()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<input type="hidden" name="last_name" value="Doe">';
        $result = (string) $this->form->hidden('last_name');
        $this->assertEquals($expected, $result);
    }

    public function test_old_input_takes_precedence_over_binding()
    {
        $oldInput = Mockery::mock('Laravolt\SemanticForm\OldInput\OldInputInterface');
        $oldInput->shouldReceive('hasOldInput')->andReturn(true);
        $oldInput->shouldReceive('getOldInput')->with('first_name')->andReturn('Steve');
        $this->form->setOldInputProvider($oldInput);

        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<input type="text" name="first_name" value="Steve">';
        $result = (string) $this->form->text('first_name');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_checkbox()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<input type="checkbox" name="terms" value="agree" checked="checked">';
        $result = (string) $this->form->checkbox('terms', 'agree');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_checkbox_with_boolean_value()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<input type="checkbox" name="is_admin" value="1" checked="checked">';
        $result = (string) $this->form->checkbox('is_admin', 1);
        $this->assertEquals($expected, $result);
    }

    public function test_value_takes_precedence_over_binding()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<input type="text" name="first_name" value="Mike">';
        $result = (string) $this->form->text('first_name')->value('Mike');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_unset_property()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $expected = '<input type="text" name="not_set">';
        $result = (string) $this->form->text('not_set');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_magic_property()
    {
        $object = new MagicGetter;
        $this->form->bind($object);
        $expected = '<input type="text" name="not_set" value="foo">';
        $result = (string) $this->form->text('not_set');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_array()
    {
        $model = ['first_name' => 'John'];
        $this->form->bind($model);
        $expected = '<input type="text" name="first_name" value="John">';
        $result = (string) $this->form->text('first_name');
        $this->assertEquals($expected, $result);
    }

    public function test_bind_nested_array()
    {
        $model = ['address' => ['street' => 'Petaling']];
        $this->form->bind($model);
        $expected = '<input type="text" name="address[street]" value="Petaling">';
        $result = (string) $this->form->text('address[street]');
        $this->assertEquals($expected, $result);
    }

    public function test_close_unbinds_model()
    {
        $object = $this->getStubObject();
        $this->form->bind($object);
        $this->form->close();
        $expected = '<input type="text" name="first_name">';
        $result = (string) $this->form->text('first_name');
        $this->assertEquals($expected, $result);
    }

    public function test_against_xss_attacks_in_bound_models()
    {
        $object = $this->getStubObject();
        $object->first_name = '" onmouseover="alert(\'xss\')';
        $this->form->bind($object);
        $expected = '<input type="text" name="first_name" value="&quot; onmouseover=&quot;alert(&#039;xss&#039;)">';
        $result = (string) $this->form->text('first_name');
        $this->assertEquals($expected, $result);
    }

    public function test_remove_class()
    {
        $expected = '<input type="text" name="food">';
        $result = (string) $this->form->text('food')
            ->addClass('sandwich pizza')
            ->removeClass('sandwich')
            ->removeClass('pizza');
        $this->assertEquals($expected, $result);
    }

    public function test_get_type_attribute()
    {
        $expected = 'radio';
        $result = $this->form->radio('fm-transmission')->getAttribute('type');
        $this->assertEquals($expected, $result);
    }

    public function test_field_callback()
    {
        $expected = '<div class="field inline"><label>Name</label><input type="text" name="name"></div>';
        $callback = function (Laravolt\SemanticForm\Elements\Field $field) {
            $field->addClass('inline');
        };
        $result = $this->form->text('name')->label('Name', $callback)->render();

        $this->assertEquals($expected, $result);
    }

    public function test_action_with_single_component()
    {
        $expected = '<div class="actions action pushed"><button type="submit" class="ui button primary" themed name="submit">Sign In</button></div>';
        $submit = $this->form->submit('Sign In', 'submit');
        $result = $this->form->action($submit)->render();

        $this->assertEquals($expected, $result);
    }

    public function test_action_with_multiple_component()
    {
        $expected = '<div class="actions action pushed">'.
            '<button type="submit" class="ui button primary" themed name="submit">Sign In</button>'.
            '<button type="button" class="ui button" themed>Cancel</button>'.
            '</div>';
        $submit = $this->form->submit('Sign In', 'submit');
        $cancel = $this->form->button('Cancel');
        $result = $this->form->action([$submit, $cancel])->render();
        $this->assertEquals($expected, $result);

        $result = $this->form->action($submit, $cancel)->render();
        $this->assertEquals($expected, $result);
    }

    public function test_action_with_macro()
    {
        $expected = '<div class="actions action pushed">'.
            '<button type="submit" class="ui button primary" themed>Submit</button>'.
            '<button type="button" class="ui button" themed>Cancel</button>'.
            '</div>';

        $form = $this->form;

        SemanticForm::macro('submit', function () use ($form) {
            return $form->submit('Submit');
        });

        SemanticForm::macro('cancel', function () use ($form) {
            return $form->button('Cancel');
        });

        $result = $this->form->action('submit', 'cancel')->render();

        $this->assertEquals($expected, $result);
    }

    private function getStubObject()
    {
        $obj = new stdClass;
        $obj->email = 'johndoe@example.com';
        $obj->first_name = 'John';
        $obj->last_name = 'Doe';
        $obj->date_of_birth = '1985-05-06';
        $obj->gender = 'male';
        $obj->terms = 'agree';
        $obj->is_admin = true;
        $obj->number = 0;
        $obj->skills = ['php', 'java'];

        return $obj;
    }
}

class MagicGetter
{
    public function __get($key)
    {
        return 'foo';
    }
}
