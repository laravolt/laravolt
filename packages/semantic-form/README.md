# Semantic Form
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/7378998a-4d74-43aa-841a-d85b74579734.svg)](https://insight.sensiolabs.com/projects/7378998a-4d74-43aa-841a-d85b74579734)
[![Travis](https://img.shields.io/travis/laravolt/semantic-form.svg)](https://travis-ci.org/laravolt/semantic-form)
[![Coverage Status](https://coveralls.io/repos/github/laravolt/semantic-form/badge.svg)](https://coveralls.io/github/laravolt/semantic-form?branch=master)

[Semantic UI](http://semantic-ui.com/) form builder, for Laravel.

## Installation

``` bash
$ composer require laravolt/semantic-form
```

## API
**Note: You can use either facade `Form::method()` or helper `form()->method()`.**

### Opening Form
``` php
Form::open('search'); // action="search"
Form::open()->get();
Form::open()->post();
Form::open()->put();
Form::open()->patch();
Form::open()->delete();
Form::open(); // default to method="GET"
Form::open()->action('search');
Form::open()->url('search'); // alias for action()
Form::open()->route('route.name');
Form::open()->post()->action(route('comment.store'));
```

### Opening Form (Short Syntax, Since 1.10)
``` php
Form::open('search'); // action="search" method=POST
Form::get('search'); // action="search" method=GET
Form::post('search'); // action="search" method=POST
Form::put('search'); // action="search" method=POST _method=PUT
Form::patch('search'); // action="search" method=POST _method=PATCH
Form::delete('search'); // action="search" method=POST _method=DELETE
```

### CSRF Token
CSRF token will automatically generated if current form method is `POST`. To prevent token generation, 
you can call `withoutToken()` when opening a form.

``` php
Form::post('search')->withoutToken();
```

### Text
``` php
Form::text($name, $value)->label('Username');
```

### Number
``` php
Form::number($name, $integerValue)->label('Total');
```

### Rupiah
``` php
Form::rupiah($name, $defaultValue = null)->label('Price');
```

### Date
``` php
Form::date($name, $value)->label('Birthday');
```

### Time
``` php
Form::time($name, $value)->label('Start Time');
```

### Password
``` php
Form::password($name)->label('Password');
```

### Email
``` php
Form::email($name, $value)->label('Email Address');
```
### Textarea
``` php
Form::textarea($name, $value)->label('Note');
```

### Select Box (Dropdown)
``` php
Form::select($name, $options)->label('Choose Country');
Form::select($name, $options, $selected)->label('Choose Country');
Form::select($name, $options)->placeholder('--Select--');
Form::select($name, $options)->appendOption($key, $label);
Form::select($name, $options)->prependOption($key, $label);
```

### Select Date & Select Date Time
``` php
Form::selectDate('myDate', $startYear, $endYear)->label('Birth Date');
Form::selectDateTime('myDate', $startYear, $endYear, $intervalInMinute)->label('Schedule');
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
Form::selectRange($name, $begin, $end)->label('Number of child');
```

### Select Month
``` php
Form::selectMonth($name, $format = '%B')->label('Month');
```

### Radio
``` php
$checked = true;
Form::radio($name, $value, $checked)->label('Item Label');
```

### Radio Group
``` php
$values = ['apple' => 'Apple', 'banana' => 'Banana'];
$checkedValue = 'banana';
Form::radioGroup($name, $values, $checkedValue)->label('Select Fruit');
```

### Checkbox
``` php
Form::checkbox($name, $value, $checked)->label('Remember Me');
```

### Checkbox Group
``` php
$values = ['apple' => 'Apple', 'banana' => 'Banana'];
$checkedValue = 'banana';
Form::checkboxGroup($name, $values, $checkedValue)->label('Select Fruit');
```

### File
``` php
Form::file($name);
```
### Input Wrapper
``` php
Form::input($name, $defaultvalue);
Form::input($name, $defaultvalue)->appendIcon('search');
Form::input($name, $defaultvalue)->prependIcon('users');
Form::input($name, $defaultvalue)->appendLabel($label);
Form::input($name, $defaultvalue)->prependLabel($label);
Form::input($name, $defaultvalue)->type("password");
```
Reference: http://semantic-ui.com/elements/input.html


### Datepicker
``` php
Form::datepicker($name, $value, $format);

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

### Timepicker
``` php
Form::timepicker($name, $value);
```

### Hidden
``` php
Form::hidden($name, $value);
```

### Button
``` php
Form::button($value);
```

### Submit
``` php
Form::submit($value);
```

### Model Binding

#### Version 1
``` php
{!! Form::bind($model) !!}
```

#### Version 2
``` php
// as parameter for method open()
{!! Form::open($route, $model) !!}

// or chaining it
{!! Form::bind($model)->get($route) !!}
```

#### Warning
```php 
// This is OK in version 1, but will produce error in version 2
{!! Form::bind($model) !!}
```


### Macro
Macro definition, put it anywhere within your application, e.g. AppServiceProvider:

```php
Form::macro('trix', function ($id, $name, $value = null) {
    return sprintf(
        "%s %s", 
        Form::hidden($name, $defaultValue)->id($id), 
        "<trix-editor input='{$id}'></trix-editor>"
    );
});
```

And then call it like any other method:
```php
Form::trix('contentId', 'content', '<b>some content</b>');
```

### Action
``` php
// Method 1

Form::action(Form::submit('Save'), Form::button('cancel'));

// Method 2

// Assumed you already define some macros:
Form::macro('submit', function(){
    return form()->submit('Submit');
});

Form::macro('cancel', function(){
    return form()->button('Cancel');
});

// Then you can just call macro name as string
Form::action('submit', 'cancel');

// Method 3

// Even further, you can define macro thats just wrap several buttons:
SemanticForm::macro('default', function(){
    return new \Laravolt\SemanticForm\Elements\Wrapper(form()->submit('Submit'), form()->submit('Submit'));
});

// And then make the call simplier:
Form::action('default');
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
Form::text($name, $value)->label('Username')->id('username')->addClass('foo');
Form::text($name, $value)->label('Username')->data('url', 'http://id-laravel.com');
Form::password($name, $value)->label('Password')->hint('Minimum 6 characters');
Form::password($name, $value)->label('Password')->hint('Minimum 6 characters', 'my-custom-css-class');
```

### Middleware

* \Laravolt\SemanticForm\Middleware\SelectDateMiddleware
* \Laravolt\SemanticForm\Middleware\SelectDateTimeMiddleware


## Credits
SemanticForm inspired by [AdamWathan\Form](https://github.com/adamwathan/form).
