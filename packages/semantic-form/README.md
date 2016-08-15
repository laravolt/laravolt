# Semantic Form
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/7378998a-4d74-43aa-841a-d85b74579734.svg)](https://insight.sensiolabs.com/projects/7378998a-4d74-43aa-841a-d85b74579734)
[![Travis](https://img.shields.io/travis/laravolt/semantic-form.svg)](https://travis-ci.org/laravolt/semantic-form)

[Semantic UI](http://semantic-ui.com/) form builder, for Laravel.

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

### Select Box (Dropdown)
``` php
SemanticForm::select($name, $options)->label('Choose Country');
SemanticForm::select($name, $options, $selected)->label('Choose Country');
SemanticForm::select($name, $options)->placeholder('--Select--');
SemanticForm::select($name, $options)->appendOption($key, $label);
SemanticForm::select($name, $options)->prependOption($key, $label);
```

### Select Date & Date Time
``` php
SemanticForm::selectDate('myDate', $startYear, $endYear)->label('Birth Date');
SemanticForm::selectDateTime('myDate', $startYear, $endYear, $intervalInMinute)->label('Schedule');
```

By default, selectDate and selectDateTime will post request as `_myDate` with `['date'=>4, 'month'=>5, 'year'=>2016]` for example.
To get `2016-5-4` format, you need to register middleware and use it in the routes.

```php
protected $routeMiddleware = [
    'selectdate' => \Laravolt\SemanticForm\Middleware\SelectDateMiddleware::class,
    'selectdatetime' => \Laravolt\SemanticForm\Middleware\SelectDateTimeMiddleware::class
];
```

```php
Route::post('myForm', ['middleware' => ['web', 'selectdate:myDate'], function (\Illuminate\Http\Request $request) {
	dd($request->input('myDate')); // Will output 2016-5-4
}]);
```

### Select Range
``` php
SemanticForm::selectRange($name, $begin, $end)->label('Number of child');
```

### Select Month
``` php
SemanticForm::selectMonth($name, $format = '%B')->label('Month');
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
### Input Wrapper
``` php
SemanticForm::input($name, $defaultvalue);
SemanticForm::input($name, $defaultvalue)->appendIcon('search');
SemanticForm::input($name, $defaultvalue)->prependIcon('users');
SemanticForm::input($name, $defaultvalue)->appendLabel($label);
SemanticForm::input($name, $defaultvalue)->prependLabel($label);
```
Reference: http://semantic-ui.com/elements/input.html

### Image (Not Yet Implemented)
``` php
SemanticForm::image($name);
```

### Datepicker (experimental)
``` php
SemanticForm::datepicker($name, $value, $format);
```
See https://github.com/Semantic-Org/Semantic-UI/pull/3256 for further discussion.

### Redactor (Not Yet Implemented)
``` php
SemanticForm::redactor($name, $value)->label('Post Body');
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

### General Function
For every form element, you can call and chaining following methods:

* id($string)
* addClass($string)
* removeClass($string)
* attribute($name, $value)
* data($name, $value)

#### Example
``` php
SemanticForm::text($name, $value)->label('Username')->id('username')->addClass('foo');
SemanticForm::text($name, $value)->label('Username')->data('url', 'http://id-laravel.com');
```

### Middleware

* \Laravolt\SemanticForm\Middleware\SelectDateMiddleware
* \Laravolt\SemanticForm\Middleware\SelectDateTimeMiddleware


## Credits
SemanticForm built on top of awesome form builder by [AdamWathan\Form](https://github.com/adamwathan/form).
