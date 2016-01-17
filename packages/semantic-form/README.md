# Semantic Form
Semantic UI form builder, for Laravel.

## Installation

Via Composer

``` bash
$ composer require laravolt/semantic-form
```

## Service Provider
``` php
Laravolt\SemanticForm\ServiceProvider::class,
```

## Facade (Alias)
``` php
'SemanticForm'    => Laravolt\SemanticForm\Facade::class,
```

## API

### Opening Form
``` php
SemanticForm::open()->get();
SemanticForm::open()->post();
SemanticForm::open()->put();
SemanticForm::open()->patch();
SemanticForm::open()->delete();
SemanticForm::open(); // default to method="GET"
SemanticForm::open()->action('search');
SemanticForm::open()->post()->action(route('comment.store'));

```

### Input Text
``` php
SemanticForm::text($name, $value)->label('Username');
```

### Password
``` php
SemanticForm::text($name, $value)->label('Password');
```

### Email
``` php
SemanticForm::email($name, $value)->label('Email Address');
```
### Textarea
``` php
SemanticForm::textarea($name, $value)->label('Note');
```

### Redactor
``` php
SemanticForm::redactor($name, $value)->label('Post Body');
```

### Select Box (Dropdown)
``` php
SemanticForm::select($name, $options)->label('Choose Country');
SemanticForm::select($name, $options, $selected)->label('Choose Country');
```

### Radio
``` php
$checked = true;
SemanticForm::radio($name, $value, $checked)->label('Item Label');
```
### Radio Group
``` php
$values = ['apple' => 'Apple', 'banana' => 'Banana'];
$checkedValue = 'banana';
SemanticForm::radioGroup($name, $values, $checkedValue)->label('Select Fruit');
```

### Checkbox
``` php
SemanticForm::checkbox($name, $value, $checked)->label('Remember Me');
```

### Checkbox Group
``` php
$values = ['apple' => 'Apple', 'banana' => 'Banana'];
$checkedValue = 'banana';
SemanticForm::checkboxGroup($name, $values, $checkedValue)->label('Select Fruit');
```

### File
``` php
SemanticForm::file($name);
```
### Image
``` php
SemanticForm::image($name);
```

### Datepicker
``` php
SemanticForm::datepicker($name, $value, $format);
```

### Hidden
``` php
SemanticForm::hidden($name, $value);
```

### Button
``` php
SemanticForm::button($value);
```

### Submit
``` php
SemanticForm::submit($value);
```

### Model Binding
``` php
SemanticForm::bind($model);
```


## Credits
SemanticForm built on top of awesome form builder by AdamWathan\Form.
