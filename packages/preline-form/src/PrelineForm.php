<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm;

use Closure;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Traits\Macroable;
use Laravolt\PrelineForm\Elements\Button;
use Laravolt\PrelineForm\Elements\Checkbox;
use Laravolt\PrelineForm\Elements\CheckboxGroup;
use Laravolt\PrelineForm\Elements\Email;
use Laravolt\PrelineForm\Elements\Field;
use Laravolt\PrelineForm\Elements\File;
use Laravolt\PrelineForm\Elements\FormOpen;
use Laravolt\PrelineForm\Elements\Hidden;
use Laravolt\PrelineForm\Elements\InputWrapper;
use Laravolt\PrelineForm\Elements\Number;
use Laravolt\PrelineForm\Elements\Password;
use Laravolt\PrelineForm\Elements\RadioButton;
use Laravolt\PrelineForm\Elements\RadioGroup;
use Laravolt\PrelineForm\Elements\Select;
use Laravolt\PrelineForm\Elements\SelectMultiple;
use Laravolt\PrelineForm\Elements\Text;
use Laravolt\PrelineForm\Elements\TextArea;
use Laravolt\PrelineForm\ErrorStore\ErrorStoreInterface;
use Laravolt\PrelineForm\OldInput\OldInputInterface;

class PrelineForm
{
    use Macroable;

    public static $displayNullValueAs = 'N/A';

    private $oldInput;

    private ErrorStore\IlluminateErrorStore $errorStore;

    private $model;

    private $config = [];

    /**
     * PrelineForm constructor.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

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

        if (! is_null($value = $this->getValueFor($name))) {
            $text->value($value);
        }

        $text->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $text->setError($this->errorStore->getError($name));
        }

        return $text;
    }

    public function number($name, $defaultValue = null)
    {
        $number = new Number($name);

        if (! is_null($value = $this->getValueFor($name))) {
            $number->value($value);
        }

        $number->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $number->setError($this->errorStore->getError($name));
        }

        return $number;
    }

    public function email($name, $defaultValue = null)
    {
        $email = new Email($name);

        if (! is_null($value = $this->getValueFor($name))) {
            $email->value($value);
        }

        $email->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $email->setError($this->errorStore->getError($name));
        }

        return $email;
    }

    public function password($name)
    {
        $password = new Password($name);

        if ($this->hasError($name)) {
            $password->setError($this->errorStore->getError($name));
        }

        return $password;
    }

    public function textarea($name, $defaultValue = null)
    {
        $textarea = new TextArea($name);

        if (! is_null($value = $this->getValueFor($name))) {
            $textarea->value($value);
        }

        $textarea->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $textarea->setError($this->errorStore->getError($name));
        }

        return $textarea;
    }

    public function select($name, $options = [], $defaultValue = null)
    {
        $select = new Select($name, $options);

        if (! is_null($value = $this->getValueFor($name))) {
            $select->value($value);
        }

        $select->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $select->setError($this->errorStore->getError($name));
        }

        return $select;
    }

    public function checkbox($name, $value = 1, $checked = null)
    {
        $checkbox = new Checkbox($name, $value);

        if (! is_null($checkedValue = $this->getValueFor($name))) {
            $checkbox->checked($checkedValue);
        }

        $checkbox->defaultChecked($checked);

        if ($this->hasError($name)) {
            $checkbox->setError($this->errorStore->getError($name));
        }

        return $checkbox;
    }

    public function radio($name, $value = null, $checked = null)
    {
        $radio = new RadioButton($name, $value);

        if (! is_null($checkedValue = $this->getValueFor($name))) {
            $radio->checked($checkedValue === $value);
        }

        $radio->defaultChecked($checked);

        if ($this->hasError($name)) {
            $radio->setError($this->errorStore->getError($name));
        }

        return $radio;
    }

    public function radioGroup($name, $options = [], $checkedOption = null)
    {
        $radioGroup = new RadioGroup($name, $options);

        if (! is_null($checkedValue = $this->getValueFor($name))) {
            $radioGroup->checked($checkedValue);
        }

        $radioGroup->defaultChecked($checkedOption);

        if ($this->hasError($name)) {
            $radioGroup->setError($this->errorStore->getError($name));
        }

        return $radioGroup;
    }

    public function checkboxGroup($name, $options = [], $checkedOptions = [])
    {
        $checkboxGroup = new CheckboxGroup($name, $options);

        if (! is_null($checkedValue = $this->getValueFor($name))) {
            $checkboxGroup->checked($checkedValue);
        }

        $checkboxGroup->defaultChecked($checkedOptions);

        if ($this->hasError($name)) {
            $checkboxGroup->setError($this->errorStore->getError($name));
        }

        return $checkboxGroup;
    }

    public function file($name)
    {
        $file = new File($name);

        if ($this->hasError($name)) {
            $file->setError($this->errorStore->getError($name));
        }

        return $file;
    }

    public function hidden($name, $value = null)
    {
        $hidden = new Hidden($name);

        if (! is_null($hiddenValue = $this->getValueFor($name))) {
            $hidden->value($hiddenValue);
        }

        $hidden->defaultValue($value);

        return $hidden;
    }

    public function submit($value = 'Submit')
    {
        return new Button($value, type: 'submit');
    }

    public function button($value = 'Button')
    {
        return new Button($value, type: 'button');
    }

    public function input($name, $defaultValue = null)
    {
        $input = new InputWrapper($name);

        if (! is_null($value = $this->getValueFor($name))) {
            $input->value($value);
        }

        $input->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $input->setError($this->errorStore->getError($name));
        }

        return $input;
    }

    public function bind($model)
    {
        $this->model = $model;

        return $this;
    }

    public function getValueFor($name)
    {
        $name = $this->normalizeName($name);

        if ($this->hasOldInput()) {
            return $this->getOldInput($name);
        }

        if ($this->hasModelValue($name)) {
            return $this->getModelValue($name);
        }

        return null;
    }

    public function hasError($name)
    {
        return $this->errorStore && $this->errorStore->hasError($name);
    }

    public function getError($name, $format = null)
    {
        if (! isset($this->errorStore)) {
            return null;
        }

        if (! $this->hasError($name)) {
            return '';
        }

        $message = $this->errorStore->getError($name);

        if ($format) {
            $message = str_replace(':message', $message, $format);
        }

        return $message;
    }

    public function make(array|BaseCollection $fields)
    {
        return new FieldCollection($fields);
    }

    public function dropdown($name, $options = [], $defaultValue = null)
    {
        return $this->select($name, $options, $defaultValue);
    }

    public function label($name)
    {
        return new Elements\Label($name);
    }

    public function link($label, $url)
    {
        return new Elements\Link($label, $url);
    }

    public function linkButton($label, $url)
    {
        return new Elements\LinkButton($label, $url);
    }

    public function html($content)
    {
        return new Elements\Html($content);
    }

    public function color($name, $defaultValue = null)
    {
        $color = new Elements\Color($name);

        if (! is_null($value = $this->getValueFor($name))) {
            $color->value($value);
        }

        $color->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $color->setError($this->errorStore->getError($name));
        }

        return $color;
    }

    public function date($name, $defaultValue = null)
    {
        $date = new Elements\Date($name);

        if (! is_null($value = $this->getValueFor($name))) {
            $date->value($value);
        }

        $date->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $date->setError($this->errorStore->getError($name));
        }

        return $date;
    }

    public function time($name, $defaultValue = null)
    {
        $time = new Elements\Time($name);

        if (! is_null($value = $this->getValueFor($name))) {
            $time->value($value);
        }

        $time->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $time->setError($this->errorStore->getError($name));
        }

        return $time;
    }

    public function selectMultiple($name, $options = [], $defaultValue = null)
    {
        $select = new SelectMultiple($name, $options);

        if (! is_null($value = $this->getValueFor($name))) {
            $select->value($value);
        }

        $select->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $select->setError($this->errorStore->getError($name));
        }

        return $select;
    }

    public function boolean(string $name, array $options = [], $checked = null)
    {
        if (empty($options)) {
            $options = [0 => 'No', 1 => 'Yes'];
        }

        return $this->radioGroup($name, $options, $checked);
    }

    public function action($actions)
    {
        $actions = collect(is_array($actions) ? $actions : func_get_args());

        $actions->transform(function ($action) {
            if (is_string($action) && static::hasMacro($action)) {
                return call_user_func_array(Closure::bind(static::$macros[$action], null, static::class), []);
            }

            return $action;
        });

        return new Elements\ActionWrapper($actions);
    }

    public function selectMonth($name, $format = '%B')
    {
        $months = [];
        foreach (range(1, 12) as $month) {
            $months[$month] = \Carbon\Carbon::createFromDate(2020, $month, 1)->translatedFormat('F');
        }

        return $this->select($name, $months);
    }

    public function selectRange($name, $begin, $end)
    {
        $range = array_combine($range = range($begin, $end), $range);

        return $this->select($name, $range);
    }

    public function openFields()
    {
        return new Elements\FieldsOpen;
    }

    public function closeFields()
    {
        return '</div>';
    }

    public function selectDate($name, $beginYear = 1900, $endYear = null)
    {
        if (! $endYear) {
            $endYear = date('Y') + 10;
        }

        $date = (new Field($this->selectRange('_'.$name.'[date]', 1, 31)));
        $month = (new Field($this->selectMonth('_'.$name.'[month]')));
        $year = (new Field($this->selectRange('_'.$name.'[year]', $beginYear, $endYear)));

        return new Elements\SelectDateWrapper($date, $month, $year);
    }

    public function selectDateTime($name, $beginYear = 1900, $endYear = null, $interval = 30)
    {
        if (! $endYear) {
            $endYear = date('Y') + 10;
        }

        $date = (new Field($this->selectRange('_'.$name.'[date]', 1, 31)));
        $month = (new Field($this->selectMonth('_'.$name.'[month]')));
        $year = (new Field($this->selectRange('_'.$name.'[year]', $beginYear, $endYear)));

        $timeOptions = $this->getTimeOptions($interval);

        $time = (new Field($this->select('_'.$name.'[time]', $timeOptions)));

        $control = new Elements\SelectDateTimeWrapper($date, $month, $year, $time);

        if (! is_null($value = $this->getValueFor($name))) {
            $control->value($value);
        }

        return $control;
    }

    public function dropdownColor($name, $defaultValue)
    {
        // For now, return a simple select with basic colors
        $options = [
            'red' => 'Red',
            'blue' => 'Blue',
            'green' => 'Green',
            'yellow' => 'Yellow',
            'purple' => 'Purple',
            'pink' => 'Pink',
            'gray' => 'Gray',
        ];

        return $this->radioGroup($name, $options, $defaultValue);
    }

    public function datepicker($name, $defaultValue = null, $format = 'YYYY-MM-DD')
    {
        // For now, return a regular date input
        return $this->date($name, $defaultValue);
    }

    /** TODO: Implement datetimepicker */
    // public function datetimepicker($name, $defaultValue = null, $format = 'Y-m-d H:i:s')
    // {
    //     // For now, return a regular datetime-local input
    //     $datetime = new \Laravolt\PrelineForm\Elements\DateTime($name);

    //     if (! is_null($value = $this->getValueFor($name))) {
    //         $datetime->value($value);
    //     }

    //     $datetime->defaultValue($defaultValue);

    //     if ($this->hasError($name)) {
    //         $datetime->setError($this->errorStore->getError($name));
    //     }

    //     return $datetime;
    // }

    public function timepicker($name, $defaultValue = null)
    {
        // For now, return a regular time input
        return $this->time($name, $defaultValue);
    }

    public function redactor($name, $defaultValue = null)
    {
        // For now, return a textarea with a note that this is a rich text field
        $textarea = $this->textarea($name, $defaultValue);
        $textarea->addClass('redactor-placeholder');

        return $textarea;
    }

    public function coordinate($name, $defaultValue = null)
    {
        // For now, return a readonly text input
        $text = $this->text($name, $defaultValue);
        $text->readonly();

        return $text;
    }

    public function dropdownDB($name, $query, $keyColumn = null, $valueColumn = null)
    {
        // For now, return an empty select - this would need database integration
        return $this->select($name, []);
    }

    public function uploader($name)
    {
        // For now, return a file input
        return $this->file($name);
    }

    public function rupiah($name, $defaultValue = null)
    {
        // For now, return a number input with currency formatting class
        $number = $this->number($name, $defaultValue);
        $number->addClass('currency-input');

        return $number;
    }

    public function multirow($name, $definition)
    {
        // For now, return HTML with a note that this is a complex field
        return $this->html('<div class="multirow-placeholder">Multirow field: '.$name.'</div>');
    }

    protected function unbindModel()
    {
        $this->model = null;
    }

    protected function hasOldInput()
    {
        return $this->oldInput && $this->oldInput->hasOldInput();
    }

    protected function getOldInput($key)
    {
        return $this->oldInput->getOldInput($key);
    }

    protected function hasModelValue($key)
    {
        return $this->model && data_get($this->model, $key) !== null;
    }

    protected function getModelValue($key)
    {
        return data_get($this->model, $key);
    }

    protected function getTimeOptions($interval)
    {
        $times = [];
        $today = \Carbon\Carbon::create(1970, 01, 01, 0, 0, 0);
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
        if (mb_substr($name, -2) === '[]') {
            return mb_substr($name, 0, -2);
        }

        return $name;
    }
}
