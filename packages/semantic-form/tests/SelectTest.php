<?php

declare(strict_types=1);

use Laravolt\SemanticForm\Elements\Select;

class SelectTest extends PHPUnit\Framework\TestCase
{
    public function test_can_render_basic_select()
    {
        $select = new Select('birth_year');
        $expected = '<select class="ui dropdown search clearable selection" name="birth_year"></select>';
        $result = $select->render();
        $this->assertEquals($expected, $result);

        $select = new Select('color');
        $expected = '<select class="ui dropdown search clearable selection" name="color"></select>';
        $result = $select->render();
        $this->assertEquals($expected, $result);
    }

    public function test_select_can_be_created_with_options()
    {
        $select = new Select('birth_year', [1990, 1991, 1992]);
        $expected = '<select class="ui dropdown search clearable selection" name="birth_year"><option value="0">1990</option><option value="1">1991</option><option value="2">1992</option></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);

        $select = new Select('birth_year', [2001, 2002, 2003]);
        $expected = '<select class="ui dropdown search clearable selection" name="birth_year"><option value="0">2001</option><option value="1">2002</option><option value="2">2003</option></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);
    }

    public function test_select_can_be_created_with_key_value_options()
    {
        $select = new Select('color', ['red' => 'Red', 'blue' => 'Blue']);
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="red">Red</option><option value="blue">Blue</option></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit', ['apple' => 'Granny Smith', 'berry' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple">Granny Smith</option><option value="berry">Blueberry</option></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_render_with_hint()
    {
        $select = new Select('birth_year');
        $select->hint('Hint');

        $expected = '<select class="ui dropdown search clearable selection" name="birth_year"></select><div class="hint">Hint</div>';
        $result = $select->render();
        $this->assertEquals($expected, $result);
    }

    public function test_can_add_option()
    {
        $select = new Select('color', ['red' => 'Red']);
        $select->addOption('blue', 'Blue');
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="red">Red</option><option value="blue">Blue</option></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit', ['apple' => 'Granny Smith']);
        $select->addOption('berry', 'Blueberry');
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple">Granny Smith</option><option value="berry">Blueberry</option></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_set_options()
    {
        $select = new Select('color');
        $select->options(['red' => 'Red', 'blue' => 'Blue']);
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="red">Red</option><option value="blue">Blue</option></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit');
        $select->options(['apple' => 'Granny Smith', 'berry' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple">Granny Smith</option><option value="berry">Blueberry</option></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_set_selected_option()
    {
        $select = new Select('color');
        $select->options(['red' => 'Red', 'blue' => 'Blue']);
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="red">Red</option><option value="blue" selected>Blue</option></select>';
        $result = $select->select('blue')->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit');
        $select->options(['apple' => 'Granny Smith', 'berry' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple" selected>Granny Smith</option><option value="berry">Blueberry</option></select>';
        $result = $select->select('apple')->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_select_numeric_keys()
    {
        $select = new Select('fruit');
        $select->options(['1' => 'Granny Smith', '2' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="1" selected>Granny Smith</option><option value="2">Blueberry</option></select>';
        $result = $select->select('1')->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit');
        $select->options(['1' => 'Granny Smith', '2' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="1">Granny Smith</option><option value="2" selected>Blueberry</option></select>';
        $result = $select->select('2')->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_set_default_option()
    {
        $select = new Select('color', ['red' => 'Red', 'blue' => 'Blue']);
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="red">Red</option><option value="blue" selected>Blue</option></select>';
        $result = $select->defaultValue('blue')->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit', ['apple' => 'Granny Smith', 'berry' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple" selected>Granny Smith</option><option value="berry">Blueberry</option></select>';
        $result = $select->defaultValue('apple')->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit', ['apple' => 'Granny Smith', 'berry' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple">Granny Smith</option><option value="berry" selected>Blueberry</option></select>';
        $result = $select->select('berry')->defaultValue('apple')->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit', ['apple' => 'Granny Smith', 'berry' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple">Granny Smith</option><option value="berry" selected>Blueberry</option></select>';
        $result = $select->defaultValue('apple')->select('berry')->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_set_default_option_multiselect()
    {
        $select = new Select('color', ['red' => 'Red', 'blue' => 'Blue']);
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="red" selected>Red</option><option value="blue" selected>Blue</option></select>';
        $result = $select->defaultValue(['blue', 'red'])->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit', ['apple' => 'Granny Smith', 'berry' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple" selected>Granny Smith</option><option value="berry">Blueberry</option></select>';
        $result = $select->defaultValue(['apple'])->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit', ['apple' => 'Granny Smith', 'berry' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple">Granny Smith</option><option value="berry" selected>Blueberry</option></select>';
        $result = $select->select('berry')->defaultValue(['apple', 'berry'])->render();

        $this->assertEquals($expected, $result);

        $select = new Select('fruit', ['apple' => 'Granny Smith', 'berry' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="apple">Granny Smith</option><option value="berry" selected>Blueberry</option></select>';
        $result = $select->defaultValue('apple')->select(['berry'])->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_use_nested_options()
    {
        $options = [
            'Ontario' => [
                'toronto' => 'Toronto',
                'london' => 'London',
            ],
            'Quebec' => [
                'montreal' => 'Montreal',
                'quebec-city' => 'Quebec City',
            ],
        ];
        $select = new Select('color', $options);
        $expected = '<select class="ui dropdown search clearable selection" name="color"><optgroup label="Ontario"><option value="toronto">Toronto</option><option value="london">London</option></optgroup><optgroup label="Quebec"><option value="montreal">Montreal</option><option value="quebec-city">Quebec City</option></optgroup></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_use_nested_options_without_keys()
    {
        $options = [
            'Ontario' => [
                'Toronto',
                'London',
            ],
            'Quebec' => [
                'Montreal',
                'Quebec City',
            ],
        ];
        $select = new Select('color', $options);
        $expected = '<select class="ui dropdown search clearable selection" name="color"><optgroup label="Ontario"><option value="0">Toronto</option><option value="1">London</option></optgroup><optgroup label="Quebec"><option value="0">Montreal</option><option value="1">Quebec City</option></optgroup></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);
    }

    public function test_can_mix_nested_and_unnested_options()
    {
        $options = [
            'toronto' => 'Toronto',
            'london' => 'London',
            'Quebec' => [
                'montreal' => 'Montreal',
                'quebec-city' => 'Quebec City',
            ],
        ];
        $select = new Select('color', $options);
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="toronto">Toronto</option><option value="london">London</option><optgroup label="Quebec"><option value="montreal">Montreal</option><option value="quebec-city">Quebec City</option></optgroup></select>';
        $result = $select->render();

        $this->assertEquals($expected, $result);
    }

    public function test_select_can_be_created_with_integer_key_value_options()
    {
        $select = new Select('color', ['0' => 'Red', '1' => 'Blue']);
        $expected = '<select class="ui dropdown search clearable selection" name="color"><option value="0">Red</option><option value="1">Blue</option></select>';
        $result = $select->render();
        $this->assertEquals($expected, $result);

        $select = new Select('fruit', ['1' => 'Granny Smith', '0' => 'Blueberry']);
        $expected = '<select class="ui dropdown search clearable selection" name="fruit"><option value="1">Granny Smith</option><option value="0">Blueberry</option></select>';
        $result = $select->render();
        $this->assertEquals($expected, $result);
    }

    public function test_select_can_be_multiple()
    {
        $select = new Select('people');
        $expected = '<select class="ui dropdown search clearable selection" name="people[]" multiple="multiple"></select>';
        $result = $select->multiple()->render();

        $this->assertEquals($expected, $result);

        $select = new Select('people[]');
        $expected = '<select class="ui dropdown search clearable selection" name="people[]" multiple="multiple"></select>';
        $result = $select->multiple()->render();

        $this->assertEquals($expected, $result);
    }

    public function test_readonly()
    {
        $select = new Select('people');
        $expected = '<select class="ui dropdown search clearable selection" name="people[]" multiple="multiple" readonly="readonly"></select>';
        $result = $select->multiple()->readonly()->render();

        $this->assertEquals($expected, $result);
    }
}
