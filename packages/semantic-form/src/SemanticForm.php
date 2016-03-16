<?php
namespace Laravolt\SemanticForm;

use Carbon\Carbon;
use Laravolt\SemanticForm\Elements\CheckboxGroup;
use Laravolt\SemanticForm\Elements\Field;
use Laravolt\SemanticForm\Elements\SelectDateWrapper;
use Laravolt\SemanticForm\Elements\SelectDateTimeWrapper;
use Laravolt\SemanticForm\Elements\Text;
use Laravolt\SemanticForm\Elements\Password;
use Laravolt\SemanticForm\Elements\Checkbox;
use Laravolt\SemanticForm\Elements\RadioButton;
use Laravolt\SemanticForm\Elements\Button;
use Laravolt\SemanticForm\Elements\Select;
use Laravolt\SemanticForm\Elements\TextArea;
use Laravolt\SemanticForm\Elements\Label;
use Laravolt\SemanticForm\Elements\FormOpen;
use Laravolt\SemanticForm\Elements\Hidden;
use Laravolt\SemanticForm\Elements\File;
use Laravolt\SemanticForm\Elements\Date;
use Laravolt\SemanticForm\Elements\Email;
use Laravolt\SemanticForm\OldInput\OldInputInterface;
use Laravolt\SemanticForm\ErrorStore\ErrorStoreInterface;

class SemanticForm
{
    private $oldInput;
    private $errorStore;
    private $csrfToken;
    private $model;

    public function setOldInputProvider(OldInputInterface $oldInputProvider)
    {
        $this->oldInput = $oldInputProvider;
    }

    public function setErrorStore(ErrorStoreInterface $errorStore)
    {
        $this->errorStore = $errorStore;
    }

    public function setToken($token)
    {
        $this->csrfToken = $token;
    }

    public function open()
    {
        $open = new FormOpen;

        if ($this->hasToken()) {
            $open->token($this->csrfToken);
        }

        return $open;
    }

    protected function hasToken()
    {
        return isset($this->csrfToken);
    }

    public function close()
    {
        $this->unbindModel();

        return '</form>';
    }

    public function text($name)
    {
        $text = new Text($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $text->value($value);
        }

        return $text;
    }

    public function date($name)
    {
        $date = new Date($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $date->value($value);
        }

        return $date;
    }

    public function email($name)
    {
        $email = new Email($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $email->value($value);
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

    public function textarea($name)
    {
        $textarea = new TextArea($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $textarea->value($value);
        }

        return $textarea;
    }

    public function password($name)
    {
        return new Password($name);
    }

    public function checkbox($name, $value = 1)
    {
        $checkbox = new Checkbox($name, $value);

        $oldValue = $this->getValueFor($name);

        if ($value == $oldValue) {
            $checkbox->check();
        }

        return $checkbox;
    }

    public function checkboxGroup($name, $options)
    {
        $controls = [];
        $oldValue = $this->getValueFor($name);

        foreach($options as $value => $label) {
            $radio = (new Checkbox($name . "[$value]", $value))->label($label);
            if ($value == $oldValue) {
                $radio->check();
            }

            $controls[] = $radio;
        }

        return new CheckboxGroup($controls);
    }

    public function radio($name, $value = null)
    {
        $value = is_null($value) ? $name : $value;

        $radio = new RadioButton($name, $value);

        $oldValue = $this->getValueFor($name);

        if ($value == $oldValue) {
            $radio->check();
        }

        return $radio;
    }

    public function radioGroup($name, $options)
    {
        $controls = [];
        $oldValue = $this->getValueFor($name);

        foreach($options as $value => $label) {
            $radio = (new RadioButton($name, $value))->label($label);
            if ($value == $oldValue) {
                $radio->check();
            }

            $controls[] = $radio;
        }

        return new CheckboxGroup($controls);
    }

    public function button($value, $name = null)
    {
        return new Button($value, $name);
    }

    public function submit($value = 'Submit')
    {
        $submit = new Button($value);
        $submit->attribute('type', 'submit');

        return $submit;
    }

    public function select($name, $options = array(), $defaultValue = null)
    {
        $select = new Select($name, $options);

        $selected = $this->getValueFor($name);
        $select->select($selected);

        $select->defaultValue($defaultValue);

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

    public function token()
    {
        $token = $this->hidden('_token');

        if (isset($this->csrfToken)) {
            $token->value($this->csrfToken);
        }

        return $token;
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
        $this->model = is_array($model) ? (object)$model : $model;
    }

    public function getValueFor($name)
    {
        if ($this->hasOldInput()) {
            return $this->getOldInput($name);
        }

        if ($this->hasModelValue($name)) {
            return $this->getModelValue($name);
        }

        return null;
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
        return $this->escape($this->oldInput->getOldInput($name));
    }

    protected function hasModelValue($name)
    {
        if (!isset($this->model)) {
            return false;
        }

        return isset($this->model->{$name}) || method_exists($this->model, '__get');
    }

    protected function getModelValue($name)
    {
        return $this->escape($this->model->{$name});
    }

    protected function escape($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        return htmlentities($value, ENT_QUOTES, 'UTF-8');
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

        return $this->select($name, $months);
    }

    public function selectRange($name, $begin, $end)
    {
        $range = array_combine($range = range($begin, $end), $range);

        return $this->select($name, $range);
    }

    public function selectDate($name, $beginYear = 1900, $endYear = null)
    {
        if (!$endYear) {
            $endYear = date('Y') + 10;
        }

        $date = (new Field($this->selectRange('_'.$name . '[date]', 1, 31)->addClass('compact')));
        $month = (new Field($this->selectMonth('_'.$name . '[month]')->addClass('compact')));
        $year = (new Field($this->selectRange('_'.$name . '[year]', $beginYear, $endYear)->addClass('compact')));

        return new SelectDateWrapper($date, $month, $year);
    }

    public function selectDateTime($name, $beginYear = 1900, $endYear = null, $interval = 30)
    {
        if (!$endYear) {
            $endYear = date('Y') + 10;
        }

        $date = (new Field($this->selectRange('_'.$name . '[date]', 1, 31)->addClass('compact')));
        $month = (new Field($this->selectMonth('_'.$name . '[month]')->addClass('compact')));
        $year = (new Field($this->selectRange('_'.$name . '[year]', $beginYear, $endYear)->addClass('compact')));

        $timeOptions = $this->getTimeOptions($interval);

        $time = (new Field($this->select('_'.$name . '[time]', $timeOptions)->addClass('compact')));

        return new SelectDateTimeWrapper($date, $month, $year, $time);
    }

    protected function getTimeOptions($interval)
    {
        $times = [];
        $today = Carbon::create(1970, 01, 01, 0, 0, 0);
        $tomorrow = clone $today;
        $tomorrow->addDay(1);

        while($today < $tomorrow) {
            $key = $val = sprintf('%s:%s', $today->format('H'), $today->format('i'));
            $times[$key] = $val;

            $today->addMinutes($interval);
        }

        return $times;
    }
}
