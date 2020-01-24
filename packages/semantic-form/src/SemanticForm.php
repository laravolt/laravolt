<?php

namespace Laravolt\SemanticForm;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Traits\Macroable;
use Laravolt\SemanticForm\Elements\ActionWrapper;
use Laravolt\SemanticForm\Elements\Button;
use Laravolt\SemanticForm\Elements\Checkbox;
use Laravolt\SemanticForm\Elements\CheckboxGroup;
use Laravolt\SemanticForm\Elements\Coordinate;
use Laravolt\SemanticForm\Elements\Date;
use Laravolt\SemanticForm\Elements\Datepicker;
use Laravolt\SemanticForm\Elements\DatepickerWrapper;
use Laravolt\SemanticForm\Elements\DropdownDB;
use Laravolt\SemanticForm\Elements\Email;
use Laravolt\SemanticForm\Elements\Field;
use Laravolt\SemanticForm\Elements\FieldsOpen;
use Laravolt\SemanticForm\Elements\File;
use Laravolt\SemanticForm\Elements\FormOpen;
use Laravolt\SemanticForm\Elements\Hidden;
use Laravolt\SemanticForm\Elements\Html;
use Laravolt\SemanticForm\Elements\InputWrapper;
use Laravolt\SemanticForm\Elements\Label;
use Laravolt\SemanticForm\Elements\Link;
use Laravolt\SemanticForm\Elements\Number;
use Laravolt\SemanticForm\Elements\Password;
use Laravolt\SemanticForm\Elements\RadioButton;
use Laravolt\SemanticForm\Elements\RadioGroup;
use Laravolt\SemanticForm\Elements\Redactor;
use Laravolt\SemanticForm\Elements\Rupiah;
use Laravolt\SemanticForm\Elements\Select;
use Laravolt\SemanticForm\Elements\SelectDateTimeWrapper;
use Laravolt\SemanticForm\Elements\SelectDateWrapper;
use Laravolt\SemanticForm\Elements\SelectMultiple;
use Laravolt\SemanticForm\Elements\Tabular;
use Laravolt\SemanticForm\Elements\Text;
use Laravolt\SemanticForm\Elements\TextArea;
use Laravolt\SemanticForm\Elements\Time;
use Laravolt\SemanticForm\Elements\Uploader;
use Laravolt\SemanticForm\ErrorStore\ErrorStoreInterface;
use Laravolt\SemanticForm\OldInput\OldInputInterface;

class SemanticForm
{
    use Macroable;

    private $oldInput;

    private $errorStore;

    private $model;

    public function setOldInputProvider(OldInputInterface $oldInputProvider)
    {
        $this->oldInput = $oldInputProvider;
    }

    public function setErrorStore(ErrorStoreInterface $errorStore)
    {
        $this->errorStore = $errorStore;
    }

    public function open($action = null, $model = null)
    {
        $open = new FormOpen($action);

        if ($model) {
            $this->bind($model);
        }

        return $open;
    }

    public function get($url = null)
    {
        return $this->open($url)->get();
    }

    public function post($url = null)
    {
        return $this->open($url)->post();
    }

    public function put($url = null)
    {
        return $this->open($url)->put();
    }

    public function patch($url = null)
    {
        return $this->open($url)->patch();
    }

    public function delete($url = null)
    {
        return $this->open($url)->delete();
    }

    public function close()
    {
        $this->unbindModel();

        return '</form>';
    }

    public function text($name, $defaultValue = null)
    {
        $text = new Text($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $text->value($value);
        }

        $text->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $text->setError();
        }

        return $text;
    }

    public function number($name, $defaultValue = null)
    {
        $number = new Number($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $number->value($value);
        }

        $number->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $number->setError();
        }

        return $number;
    }

    public function date($name, $defaultValue = null)
    {
        $date = new Date($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $date->value($value);
        }

        $date->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $date->setError();
        }

        return $date;
    }

    public function datepicker($name, $defaultValue = null, $format = 'Y-m-d')
    {
        $input = new Datepicker($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $input->value($value);
        }

        $input->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $input->setError();
        }

        return (new DatepickerWrapper($input))
            ->format($format)
            ->prependIcon('calendar alternate outline')
            ->addClass('calendar');
    }

    public function time($name, $defaultValue = null)
    {
        $date = new Time($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $date->value($value);
        }

        $date->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $date->setError();
        }

        return $date;
    }

    public function timepicker($name, $defaultValue = null)
    {
        $input = new Datepicker($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $input->value($value);
        }

        $input->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $input->setError();
        }

        return (new InputWrapper($input))
            ->data('calendar-type', 'time')
            ->prependIcon('clock outline outline')
            ->addClass('calendar');
    }

    public function email($name, $defaultValue = null)
    {
        $email = new Email($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $email->value($value);
        }

        $email->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $email->setError();
        }

        return $email;
    }

    public function hidden($name, $value = null)
    {
        $hidden = new Hidden($name);
        $hidden->value($value);

        if (!is_null($value = $this->getValueFor($name))) {
            $hidden->value($value);
        }

        return $hidden;
    }

    public function textarea($name, $defaultValue = null)
    {
        $textarea = new TextArea($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $textarea->value($value);
        }

        $textarea->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $textarea->setError();
        }

        return $textarea;
    }

    public function redactor($name, $defaultValue = null)
    {
        \Stolz\Assets\Laravel\Facade::add('redactor');

        $redactor = new Redactor($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $redactor->value($value);
        }

        if ($this->hasError($name)) {
            $redactor->setError();
        }

        $redactor->defaultValue($defaultValue)
            ->data('token', Session::token())
            ->data('role', 'redactor');

        return $redactor;
    }

    public function coordinate($name, $defaultValue = null)
    {
        return (new Coordinate($name))->defaultValue($defaultValue)->data('form-coordinate', 'true')->readonly();
    }

    public function password($name)
    {
        $password = new Password($name);

        if ($this->hasError($name)) {
            $password->setError();
        }

        return $password;
    }

    public function checkbox($name, $value = 1, $checked = false)
    {
        $checkbox = new Checkbox($name, $value);

        $oldValue = $this->getValueFor($name);

        if ($value == $oldValue) {
            $checkbox->check();
        }

        $checkbox->defaultCheckedState($checked);

        return $checkbox;
    }

    public function checkboxGroup($name, $options, $checked = [])
    {
        $checked = (array) $checked;
        $controls = [];
        $oldValue = $this->getValueFor($name);

        foreach ($options as $value => $label) {
            $dataAttributes = [];
            $labelText = $label;
            if (is_array($label)) {
                $labelText = Arr::pull($label, 'label');
                $dataAttributes = $label;
            }

            $radio = (new Checkbox($name."[$value]", $value))->label($labelText);

            foreach ($dataAttributes as $dataKey => $dataValue) {
                $radio->data($dataKey, $dataValue);
            }

            if ($oldValue !== null) {
                if (in_array($value, $oldValue)) {
                    $radio->check();
                }
            } else {
                if (in_array($value, $checked)) {
                    $radio->check();
                }
            }

            $controls[] = $radio;
        }

        return new CheckboxGroup($controls);
    }

    public function radio($name, $value = null, $checked = false)
    {
        $value = is_null($value) ? $name : $value;

        $radio = new RadioButton($name, $value);

        $oldValue = $this->getValueFor($name);

        if ($value == $oldValue) {
            $radio->check();
        }

        $radio->defaultCheckedState($checked);

        return $radio;
    }

    public function radioGroup($name, $options = [], $checked = null)
    {
        $controls = [];
        $oldValue = $this->getValueFor($name);

        foreach ($options as $value => $label) {
            $dataAttributes = [];
            $labelText = $label;
            if (is_array($label)) {
                $labelText = Arr::pull($label, 'label');
                $dataAttributes = $label;
            }

            $radio = (new RadioButton($name, $value))->label($labelText);

            foreach ($dataAttributes as $dataKey => $dataValue) {
                $radio->data($dataKey, $dataValue);
            }

            if (($oldValue !== null && $value == $oldValue) || ($oldValue === null && $value == $checked)) {
                $radio->check();
            }
            $controls[] = $radio;
        }

        $radioGroup = new RadioGroup($controls);
        $radioGroup->options($options);

        return $radioGroup;
    }

    public function button($value, $name = null)
    {
        return new Button($value, $name);
    }

    public function submit($label = 'Submit', $name = null)
    {
        $submit = new Button($label, $name);
        $submit->attribute('type', 'submit')->addClass('primary');

        return $submit;
    }

    public function link($label, $url)
    {
        $submit = new Link($label, $url);

        return $submit;
    }

    public function action($actions)
    {
        $actions = collect(is_array($actions) ? $actions : func_get_args());

        $actions->transform(function ($action) {
            if (is_string($action) && static::hasMacro($action)) {
                return call_user_func_array(\Closure::bind(static::$macros[$action], null, static::class), []);
            }

            return $action;
        });

        return new ActionWrapper($actions);
    }

    public function dropdown($name, $options = [], $defaultValue = null)
    {
        $select = new Select($name, $options);

        $selected = $this->getValueFor($name);
        $select->select($selected);

        $select->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $select->setError();
        }

        return $select;
    }

    public function dropdownDB($name, $query, $keyColumn = null, $valueColumn = null)
    {
        $element = (new DropdownDB($name, []))->query($query)->keyColumn($keyColumn)->label($valueColumn);

        $selected = $this->getValueFor($name);
        $element->select($selected);

        if ($this->hasError($name)) {
            $element->setError();
        }

        return $element;
    }

    /**
     * @deprecated
     */
    public function select($name, $options = [], $defaultValue = null)
    {
        return $this->dropdown($name, $options, $defaultValue);
    }

    public function selectMultiple($name, $options = [], $defaultValue = null)
    {
        $select = new SelectMultiple($name, $options);

        $selected = $this->getValueFor($name);
        $select->select($selected);

        if ($defaultValue) {
            $select->defaultValue($defaultValue);
        }

        if ($this->hasError($name)) {
            $select->setError();
        }

        return $select;
    }

    public function label($label)
    {
        return new Label($label);
    }

    public function file($name)
    {
        return new File($name);
    }

    public function uploader($name)
    {
        $uploader = (new Uploader($name))->data('token', Session::token())->data('fileuploader-listInput', "_$name");

        return $uploader;
    }

    public function rupiah($name, $defaultValue = null)
    {
        \Stolz\Assets\Laravel\Facade::group('laravolt')->add('autoNumeric');

        $text = $this->text($name, $defaultValue);
        // $input = $this->input($name, $defaultValue)->prependLabel('Rp');
        // $input->getPrimaryControl()->data('role', 'rupiah');
        $input = (new Rupiah($text));

        return $input;
    }

    public function input($name, $defaultValue = null)
    {
        $text = $this->text($name, $defaultValue);

        return new InputWrapper($text);
    }

    public function tabular($name, $definition)
    {
        $element = new Tabular($name, $definition);

        return $element;
    }

    public function hasError($name)
    {
        if (!isset($this->errorStore)) {
            return false;
        }

        return $this->errorStore->hasError($name);
    }

    public function getError($name, $format = null)
    {
        if (!isset($this->errorStore)) {
            return null;
        }

        if (!$this->hasError($name)) {
            return '';
        }

        $message = $this->errorStore->getError($name);

        if ($format) {
            $message = str_replace(':message', $message, $format);
        }

        return $message;
    }

    public function bind($model)
    {
        $this->model = is_array($model) ? (object) $model : $model;

        return $this;
    }

    public function getValueFor($name)
    {
        $name = $this->normalizeName($name);

        if ($this->hasOldInput()) {
            return $this->getOldInput($name);
        }

        return $this->getModelValue($name);
    }

    protected function hasOldInput()
    {
        if (!isset($this->oldInput)) {
            return false;
        }

        return $this->oldInput->hasOldInput();
    }

    protected function getOldInput($name)
    {
        return $this->oldInput->getOldInput($name);
    }

    protected function getModelValue($name)
    {
        $name = str_replace('[', '.', str_replace(']', '', $name));
        $value = data_get($this->model, $name, $this->model->{$name} ?? null);

        if ($value instanceof Collection) {
            $value = $value->pluck('id')->toArray();
        }

        if (is_string($value) || is_numeric($value) || is_bool($value) || is_array($value)) {
            return $value;
        }

        return null;
    }

    protected function unbindModel()
    {
        $this->model = null;
    }

    public function selectMonth($name, $format = '%B')
    {
        $months = [];
        foreach (range(1, 12) as $month) {
            $months[$month] = strftime($format, mktime(0, 0, 0, $month, 1));
        }

        return $this->dropdown($name, $months);
    }

    public function selectRange($name, $begin, $end)
    {
        $range = array_combine($range = range($begin, $end), $range);

        return $this->dropdown($name, $range);
    }

    public function selectDate($name, $beginYear = 1900, $endYear = null)
    {
        if (!$endYear) {
            $endYear = date('Y') + 10;
        }

        $date = (new Field($this->selectRange('_'.$name.'[date]', 1, 31)->addClass('compact')));
        $month = (new Field($this->selectMonth('_'.$name.'[month]')->addClass('compact')));
        $year = (new Field($this->selectRange('_'.$name.'[year]', $beginYear, $endYear)->addClass('compact')));

        return new SelectDateWrapper($date, $month, $year);
    }

    public function selectDateTime($name, $beginYear = 1900, $endYear = null, $interval = 30)
    {
        if (!$endYear) {
            $endYear = date('Y') + 10;
        }

        $date = (new Field($this->selectRange('_'.$name.'[date]', 1, 31)->addClass('compact')));
        $month = (new Field($this->selectMonth('_'.$name.'[month]')->addClass('compact')));
        $year = (new Field($this->selectRange('_'.$name.'[year]', $beginYear, $endYear)->addClass('compact')));

        $timeOptions = $this->getTimeOptions($interval);

        $time = (new Field($this->dropdown('_'.$name.'[time]', $timeOptions)->addClass('compact')));

        $control = new SelectDateTimeWrapper($date, $month, $year, $time);

        if (!is_null($value = $this->getValueFor($name))) {
            $control->value($value);
        }

        return $control;
    }

    public function openFields()
    {
        return new FieldsOpen();
    }

    public function closeFields()
    {
        return '</div>';
    }

    public function html($content)
    {
        return new Html($content);
    }

    public function make(array $fields)
    {
        return new FieldCollection($fields);
    }

    protected function getTimeOptions($interval)
    {
        $times = [];
        $today = Carbon::create(1970, 01, 01, 0, 0, 0);
        $tomorrow = clone $today;
        $tomorrow->addDay(1);

        while ($today < $tomorrow) {
            $key = $val = sprintf('%s:%s', $today->format('H'), $today->format('i'));
            $times[$key] = $val;

            $today->addMinutes($interval);
        }

        return $times;
    }

    protected function normalizeName($name)
    {
        if (substr($name, -2) == '[]') {
            return substr($name, 0, -2);
        }

        return $name;
    }
}
