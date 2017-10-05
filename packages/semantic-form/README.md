# Semantic Form
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/7378998a-4d74-43aa-841a-d85b74579734.svg)](https://insight.sensiolabs.com/projects/7378998a-4d74-43aa-841a-d85b74579734)
[![Travis](https://img.shields.io/travis/laravolt/semantic-form.svg)](https://travis-ci.org/laravolt/semantic-form)
[![Coverage Status](https://coveralls.io/repos/github/laravolt/semantic-form/badge.svg?branch=master)](https://coveralls.io/github/laravolt/semantic-form?branch=master)

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
SemanticForm::open('search'); // action="search"
SemanticForm::open()->get();
SemanticForm::open()->post();
SemanticForm::open()->put();
SemanticForm::open()->patch();
SemanticForm::open()->delete();
SemanticForm::open(); // default to method="GET"
SemanticForm::open()->action('search');
SemanticForm::open()->url('search'); // alias for action()
SemanticForm::open()->route('route.name');
SemanticForm::open()->post()->action(route('comment.store'));
```

### Opening Form (Short Syntax, Since 1.10)
``` php
SemanticForm::open('search'); // action="search" method=POST
SemanticForm::get('search'); // action="search" method=GET
SemanticForm::post('search'); // action="search" method=POST
SemanticForm::put('search'); // action="search" method=POST _method=PUT
SemanticForm::patch('search'); // action="search" method=POST _method=PATCH
SemanticForm::delete('search'); // action="search" method=POST _method=DELETE
```

### Input Text
``` php
SemanticForm::text($name, $value)->label('Username');
```

### Password
``` php
SemanticForm::password($name)->label('Password');
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
SemanticForm::input($name, $defaultvalue)->type("password");
```
Reference: http://semantic-ui.com/elements/input.html

### Image (Not Yet Implemented)
``` php
SemanticForm::image($name);
```

### Datepicker (experimental)
``` php
// somewhere in view
SemanticForm::datepicker($name, $value, $format);

// don't forget to put this somewhere on your view
@include('semantic-form::scripts.calendar')

// Valid $format are:
// DD -> two digit date
// MM -> two digit month number
// MMMM -> month name (localized)
// YY -> two digit year
// YYYY -> full year

// To convert localized format to standard (SQL) datetime format, you can use Jenssegers\Date\Date library (already included):
// Jenssegers\Date\Date::createFromFormat('d F Y', '12 februari 2000')->startOfDay()->toDateTimeString();
// Jenssegers\Date\Date::createFromFormat('d F Y', '12 februari 2000')->startOfDay()->toDateString();
```
See https://github.com/Semantic-Org/Semantic-UI/pull/3256 for further discussion. Remember, you must include calendar.js and calendar.css on your own.

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
* hint($text) (Since 1.10.0)
* hint($text, $class = "hint") (Since 1.10.2)

#### Override Hint class globally (Since 1.10.2)
``` php
// Put this on every request, e.g. in AppServiceProvider
Laravolt\SemanticForm\Elements\Hint::$defaultClass = 'custom-class';
```

#### Example
``` php
SemanticForm::text($name, $value)->label('Username')->id('username')->addClass('foo');
SemanticForm::text($name, $value)->label('Username')->data('url', 'http://id-laravel.com');
SemanticForm::password($name, $value)->label('Password')->hint('Minimum 6 characters');
SemanticForm::password($name, $value)->label('Password')->hint('Minimum 6 characters', 'my-custom-css-class');
```

### Middleware

* \Laravolt\SemanticForm\Middleware\SelectDateMiddleware
* \Laravolt\SemanticForm\Middleware\SelectDateTimeMiddleware


## Credits
SemanticForm inspired by [AdamWathan\Form](https://github.com/adamwathan/form).
