<?php

namespace Laravolt\PrelineForm;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Traits\Macroable;
use Laravolt\PrelineForm\Elements\Button;
use Laravolt\PrelineForm\Elements\Checkbox;
use Laravolt\PrelineForm\Elements\CheckboxGroup;
use Laravolt\PrelineForm\Elements\Color;
use Laravolt\PrelineForm\Elements\Date;
use Laravolt\PrelineForm\Elements\Email;
use Laravolt\PrelineForm\Elements\Field;
use Laravolt\PrelineForm\Elements\FieldsOpen;
use Laravolt\PrelineForm\Elements\File;
use Laravolt\PrelineForm\Elements\FormOpen;
use Laravolt\PrelineForm\Elements\Hidden;
use Laravolt\PrelineForm\Elements\InputWrapper;
use Laravolt\PrelineForm\Elements\Label;
use Laravolt\PrelineForm\Elements\Link;
use Laravolt\PrelineForm\Elements\Number;
use Laravolt\PrelineForm\Elements\Password;
use Laravolt\PrelineForm\Elements\RadioButton;
use Laravolt\PrelineForm\Elements\RadioGroup;
use Laravolt\PrelineForm\Elements\Select;
use Laravolt\PrelineForm\Elements\SelectMultiple;
use Laravolt\PrelineForm\Elements\Text;
use Laravolt\PrelineForm\Elements\TextArea;
use Laravolt\PrelineForm\Elements\Time;
use Laravolt\PrelineForm\ErrorStore\ErrorStoreInterface;
use Laravolt\PrelineForm\OldInput\OldInputInterface;

class PrelineForm
{
    use Macroable;

    public static $displayNullValueAs = 'N/A';

    private $oldInput;

    private $errorStore;

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
            $text->setError();
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
            $number->setError();
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
            $email->setError();
        }

        return $email;
    }

    public function password($name)
    {
        $password = new Password($name);

        if ($this->hasError($name)) {
            $password->setError();
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
            $textarea->setError();
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
            $select->setError();
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
            $checkbox->setError();
        }

        return $checkbox;
    }

    public function radio($name, $value = null, $checked = null)
    {
        $radio = new RadioButton($name, $value);

        if (! is_null($checkedValue = $this->getValueFor($name))) {
            $radio->checked($checkedValue == $value);
        }

        $radio->defaultChecked($checked);

        if ($this->hasError($name)) {
            $radio->setError();
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
            $radioGroup->setError();
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
            $checkboxGroup->setError();
        }

        return $checkboxGroup;
    }

    public function file($name)
    {
        $file = new File($name);

        if ($this->hasError($name)) {
            $file->setError();
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
        return new Button($value, 'submit');
    }

    public function button($value = 'Button')
    {
        return new Button($value, 'button');
    }

    public function input($name, $defaultValue = null)
    {
        $input = new InputWrapper($name);

        if (! is_null($value = $this->getValueFor($name))) {
            $input->value($value);
        }

        $input->defaultValue($defaultValue);

        if ($this->hasError($name)) {
            $input->setError();
        }

        return $input;
    }

    public function bind($model)
    {
        $this->model = $model;

        return $this;
    }

    protected function unbindModel()
    {
        $this->model = null;
    }

    protected function getValueFor($name)
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
        return $this->oldInput && $this->oldInput->hasOldInput();
    }

    protected function getOldInput($key)
    {
        return $this->oldInput->getOldInput($key);
    }

    protected function hasError($key)
    {
        return $this->errorStore && $this->errorStore->hasError($key);
    }

    protected function getError($key)
    {
        return $this->errorStore->getError($key);
    }

    protected function hasModelValue($key)
    {
        return $this->model && data_get($this->model, $key) !== null;
    }

    protected function getModelValue($key)
    {
        return data_get($this->model, $key);
    }
}