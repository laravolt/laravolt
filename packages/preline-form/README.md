# Preline Form

[Preline UI](https://preline.co/) form builder for Laravel, built with Tailwind CSS.

This package is based on [laravolt/semantic-form](https://github.com/laravolt/semantic-form) but adapted to use Preline UI components and Tailwind CSS classes instead of Semantic UI.

## Installation

```bash
$ composer require laravolt/preline-form
```

## Requirements

- PHP >= 7.3
- Laravel >= 7.0
- Tailwind CSS
- Preline UI (recommended)

## API

**Note: You can use either facade `PrelineForm::method()` or helper `preline_form()->method()`.**

### Opening Form

```php
PrelineForm::open('search'); // action="search"
PrelineForm::open()->get();
PrelineForm::open()->post();
PrelineForm::open()->put();
PrelineForm::open()->patch();
PrelineForm::open()->delete();
PrelineForm::open(); // default to method="GET"
PrelineForm::open()->action('search');
PrelineForm::open()->url('search'); // alias for action()
PrelineForm::open()->route('route.name');
PrelineForm::open()->post()->action(route('comment.store'));
```

### Opening Form (Short Syntax)

```php
PrelineForm::open('search'); // action="search" method=POST
PrelineForm::get('search'); // action="search" method=GET
PrelineForm::post('search'); // action="search" method=POST
PrelineForm::put('search'); // action="search" method=POST _method=PUT
PrelineForm::patch('search'); // action="search" method=POST _method=PATCH
PrelineForm::delete('search'); // action="search" method=POST _method=DELETE
```

### CSRF Token

CSRF token will automatically be generated if current form method is `POST`. To prevent token generation, you can call `withoutToken()` when opening a form.

```php
PrelineForm::post('search')->withoutToken();
```

### Text Input

```php
PrelineForm::text($name, $value)->label('Username');
PrelineForm::text($name, $value)->placeholder('Enter username');
```

### Number Input

```php
PrelineForm::number($name, $integerValue)->label('Total');
PrelineForm::number($name)->min(0)->max(100)->step(1);
```

### Date & Time Inputs

```php
PrelineForm::date($name, $value)->label('Birthday');
PrelineForm::time($name, $value)->label('Start Time');
```

### Password Input

```php
PrelineForm::password($name)->label('Password');
```

### Email Input

```php
PrelineForm::email($name, $value)->label('Email Address');
```

### Textarea

```php
PrelineForm::textarea($name, $value)->label('Note');
PrelineForm::textarea($name, $value)->rows(5)->cols(40);
```

### Select Box (Dropdown)

```php
PrelineForm::select($name, $options)->label('Choose Country');
PrelineForm::select($name, $options, $selected)->label('Choose Country');
PrelineForm::select($name, $options)->placeholder('--Select--');
PrelineForm::select($name, $options)->appendOption($key, $label);
PrelineForm::select($name, $options)->prependOption($key, $label);
```

### Multiple Select

```php
PrelineForm::selectMultiple($name, $options)->label('Choose Countries');
```

### Radio Button

```php
$checked = true;
PrelineForm::radio($name, $value, $checked)->label('Item Label');
```

### Radio Group

```php
$values = ['apple' => 'Apple', 'banana' => 'Banana'];
$checkedValue = 'banana';
PrelineForm::radioGroup($name, $values, $checkedValue)->label('Select Fruit');
```

### Checkbox

```php
PrelineForm::checkbox($name, $value, $checked)->label('Remember Me');
```

### Checkbox Group

```php
$values = ['apple' => 'Apple', 'banana' => 'Banana'];
$checkedValue = ['banana'];
PrelineForm::checkboxGroup($name, $values, $checkedValue)->label('Select Fruits');
```

### File Input

```php
PrelineForm::file($name)->label('Upload File');
PrelineForm::file($name)->accept('image/*')->multiple();
```

### Input Wrapper

```php
PrelineForm::input($name, $defaultvalue);
PrelineForm::input($name, $defaultvalue)->appendIcon('search');
PrelineForm::input($name, $defaultvalue)->prependIcon('users');
PrelineForm::input($name, $defaultvalue)->appendLabel($label);
PrelineForm::input($name, $defaultvalue)->prependLabel($label);
```

### Hidden Input

```php
PrelineForm::hidden($name, $value);
```

### Buttons

```php
PrelineForm::button($value);
PrelineForm::submit($value);

// Button variations
PrelineForm::submit('Save')->primary();
PrelineForm::button('Cancel')->secondary();
PrelineForm::submit('Delete')->danger();
PrelineForm::submit('Confirm')->success();
```

### Model Binding

```php
// as parameter for method open()
{!! PrelineForm::open($route, $model) !!}

// or chaining it
{!! PrelineForm::bind($model)->get($route) !!}
```

### Styling and Classes

All form elements come with pre-configured Preline UI and Tailwind CSS classes. You can add custom classes:

```php
PrelineForm::text($name, $value)->addClass('custom-class');
PrelineForm::text($name, $value)->removeClass('default-class');
```

### Error Handling

Form elements automatically display validation errors when using Laravel's validation:

```php
// Error styling is automatically applied when validation fails
PrelineForm::text('email')->label('Email Address');

// The field will show red border and error message if validation fails
```

### General Functions

For every form element, you can call and chain the following methods:

- `id($string)`
- `addClass($string)`
- `removeClass($string)`
- `attribute($name, $value)`
- `data($name, $value)`
- `hint($text)`
- `hint($text, $class)`

#### Example

```php
PrelineForm::text($name, $value)->label('Username')->id('username')->addClass('custom-class');
PrelineForm::text($name, $value)->label('Username')->data('url', 'http://example.com');
PrelineForm::password($name, $value)->label('Password')->hint('Minimum 6 characters');
PrelineForm::password($name, $value)->label('Password')->hint('Minimum 6 characters', 'text-red-500');
```

## Styling with Tailwind CSS

This package uses Tailwind CSS classes for styling. The default styling follows Preline UI design patterns:

### Default Input Styling

- Border: `border-gray-200`
- Focus: `focus:border-blue-500 focus:ring-blue-500`
- Dark mode: `dark:bg-slate-900 dark:border-gray-700`
- Error state: `border-red-500 focus:border-red-500`

### Buttons

- Primary: Blue background with white text
- Secondary: White background with gray border
- Danger: Red background for destructive actions
- Success: Green background for positive actions

### Form Layout

- Forms use `space-y-6` for vertical spacing
- Field groups use `space-y-4`
- Individual fields use `space-y-1`

## Customization

You can override default classes by extending the elements or by adding/removing classes:

```php
// Add custom styling
PrelineForm::text('username')
    ->addClass('border-purple-500 focus:border-purple-600')
    ->removeClass('border-gray-200 focus:border-blue-500');

// Custom button styling
PrelineForm::submit('Save')
    ->addClass('bg-purple-600 hover:bg-purple-700')
    ->removeClass('bg-blue-600 hover:bg-blue-700');
```

## Integration with Preline UI

For best results, include Preline UI CSS and JavaScript in your project:

```html
<!-- Include Preline UI CSS -->
<link href="https://preline.co/assets/css/main.min.css" rel="stylesheet" />

<!-- Include Preline UI JavaScript -->
<script src="https://preline.co/assets/js/preline.js"></script>
```

Or install via npm:

```bash
npm install preline
```

## Credits

Preline Form is inspired by and based on [laravolt/semantic-form](https://github.com/laravolt/semantic-form) by [Laravolt](https://laravolt.dev), adapted to use [Preline UI](https://preline.co/) and Tailwind CSS.
