# Preline Form

[![Latest Version](https://img.shields.io/packagist/v/laravolt/preline-form.svg)](https://packagist.org/packages/laravolt/preline-form)
[![Total Downloads](https://img.shields.io/packagist/dt/laravolt/preline-form.svg)](https://packagist.org/packages/laravolt/preline-form)
[![License](https://img.shields.io/packagist/l/laravolt/preline-form.svg)](https://packagist.org/packages/laravolt/preline-form)
[![Tests](https://github.com/laravolt/laravolt/workflows/Tests/badge.svg)](https://github.com/laravolt/laravolt/actions)

🎨 **Beautiful forms for Laravel with Preline UI and Tailwind CSS**

**✨ SemanticForm Compatible** • **🎯 Laravel Ready** • **🎨 Tailwind Styled** • **🌙 Dark Mode**

[Preline UI](https://preline.co/) form builder for Laravel, built with Tailwind CSS. This package is based on [laravolt/semantic-form](https://github.com/laravolt/semantic-form) but adapted to use Preline UI components and Tailwind CSS classes instead of Semantic UI.

## 📋 Table of Contents

- [🚀 Quick Start](#-quick-start)
- [📦 Installation](#-installation)
- [📋 Requirements](#-requirements)
- [🔄 Migration from SemanticForm](#-migration-from-semanticform)
- [📚 API Reference](#-api-reference)
- [🎨 Advanced Features](#-advanced-features)
- [💡 Complete Examples](#-complete-examples)
- [🎨 Styling & Customization](#-styling--customization)
- [🔍 Troubleshooting](#-troubleshooting)
- [🤝 Contributing](#-contributing)

## 🚀 Quick Start

After installation, add this to your Blade template:

```php
{!! PrelineForm::open('user.store')->post() !!}
    {!! PrelineForm::text('username')->label('Username')->required() !!}
    {!! PrelineForm::email('email')->label('Email Address')->required() !!}
    {!! PrelineForm::password('password')->label('Password')->required() !!}
    {!! PrelineForm::submit('Create Account')->primary() !!}
{!! PrelineForm::close() !!}
```

## 📦 Installation

```bash
composer require laravolt/preline-form
```

## 📋 Requirements

| Requirement      | Version                       | Notes                              |
| ---------------- | ----------------------------- | ---------------------------------- |
| **PHP**          | `>= 8.2`                      | Updated requirement                |
| **Laravel**      | `^10.0 \|\| ^11.0 \|\| ^12.0` | Support for latest versions        |
| **Tailwind CSS** | `^3.0`                        | Required for styling               |
| **Preline UI**   | `^2.0`                        | Recommended for full functionality |

### Browser Support

- Chrome/Edge 88+
- Firefox 85+
- Safari 14+

## 🔄 Migration from SemanticForm

PrelineForm is designed to be **API-compatible** with SemanticForm for smooth migration:

```php
// SemanticForm (old)
Form::text('username')->attributes(['class' => 'large'])

// PrelineForm (new) - same API!
PrelineForm::text('username')->attributes(['class' => 'large'])
```

### ✅ Key Compatibility Features:

- ✅ `->attributes()` method on all elements
- ✅ `->horizontal()` method for form layout
- ✅ `->make()` method for dynamic field generation
- ✅ Same method signatures and chaining patterns
- ✅ Compatible error handling and validation
- ✅ Model binding support

See [MIGRATION.md](MIGRATION.md) for detailed migration guide.

## 📚 API Reference

**Note: You can use either facade `PrelineForm::method()` or helper `form()->method()`.**

<details>
<summary><strong>📝 Text & Input Elements</strong></summary>

- [`text()`](#text-input) - Text input field
- [`email()`](#email-input) - Email input field
- [`password()`](#password-input) - Password input field
- [`number()`](#number-input) - Number input field
- [`date()`](#date--time-inputs) - Date input field
- [`time()`](#date--time-inputs) - Time input field
- [`color()`](#color-picker) - Color picker input
- [`textarea()`](#textarea) - Multi-line text area
- [`hidden()`](#hidden-input) - Hidden input field

</details>

<details>
<summary><strong>🎛️ Selection Elements</strong></summary>

- [`select()`](#select-box-dropdown) - Dropdown selection
- [`selectMultiple()`](#multiple-select) - Multiple selection
- [`radio()`](#radio-button) - Radio button
- [`radioGroup()`](#radio-group) - Radio button group
- [`checkbox()`](#checkbox) - Checkbox
- [`checkboxGroup()`](#checkbox-group) - Checkbox group

</details>

<details>
<summary><strong>📁 File & Media</strong></summary>

- [`file()`](#file-input) - File upload input

</details>

<details>
<summary><strong>🎯 Buttons & Actions</strong></summary>

- [`submit()`](#buttons) - Submit button
- [`button()`](#buttons) - Regular button
- [`link()`](#links) - Link element

</details>

<details>
<summary><strong>🔧 Utility & Layout</strong></summary>

- [`html()`](#html-content) - Custom HTML content
- [`input()`](#input-wrapper) - Input wrapper with icons
- [`make()`](#dynamic-field-generation) - Dynamic field generation

</details>

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
text(string $name, mixed $defaultValue = null): Text
```

**Parameters:**

- `$name` (string): The input name attribute
- `$defaultValue` (mixed): Default value for the input

**Example:**

```php
PrelineForm::text('username', 'john_doe')
    ->label('Username')
    ->placeholder('Enter your username')
    ->required()
    ->attributes(['maxlength' => 50]);
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

### Color Picker

```php
PrelineForm::color($name, $defaultValue)->label('Theme Color');
```

### HTML Content

```php
PrelineForm::html('<div class="alert alert-info">Custom HTML content</div>');
```

### Links

```php
PrelineForm::link('Visit Website', 'https://example.com')
    ->addClass('text-blue-600 hover:text-blue-800');
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

- `id($string)` - Set element ID
- `addClass($string)` - Add CSS class
- `removeClass($string)` - Remove CSS class
- `attribute($name, $value)` - Set single attribute
- `attributes($array)` - Set multiple attributes at once
- `data($name, $value)` - Set data attribute
- `hint($text)` - Add help text
- `hint($text, $class)` - Add help text with custom styling

#### Example

```php
PrelineForm::text($name, $value)
    ->label('Username')
    ->id('username')
    ->addClass('custom-class')
    ->attributes(['maxlength' => 50, 'data-validate' => 'true']);

PrelineForm::password($name, $value)
    ->label('Password')
    ->hint('Minimum 6 characters', 'text-red-500');
```

## 🎨 Advanced Features

### Dynamic Field Generation

Create forms dynamically using the `make()` method:

```php
$fields = [
    'name' => ['type' => 'text', 'label' => 'Full Name', 'required' => true],
    'email' => ['type' => 'email', 'label' => 'Email Address'],
    'bio' => ['type' => 'textarea', 'label' => 'Biography', 'rows' => 4],
    'country' => ['type' => 'select', 'label' => 'Country', 'options' => $countries]
];

echo PrelineForm::make($fields);
```

### Form Layout Options

```php
// Horizontal form layout
PrelineForm::open('user.store')->horizontal();

// Custom form classes
PrelineForm::open('user.store')->addClass('max-w-md mx-auto');

// Grid layout
PrelineForm::open('user.store')->addClass('grid grid-cols-2 gap-4');
```

### Advanced Validation & Error Handling

```php
// Check for errors
if (PrelineForm::hasError('username')) {
    // Handle error state
}

// Get error message
$error = PrelineForm::getError('username');

// Custom error styling
PrelineForm::text('username')
    ->addClassIf($errors->has('username'), 'border-red-500');
```

### File Upload with Validation

```php
PrelineForm::file('avatar')
    ->label('Profile Picture')
    ->accept('image/*')
    ->multiple()
    ->hint('Maximum file size: 2MB per file');
```

## 💡 Complete Examples

### User Registration Form

```php
{!! PrelineForm::open('auth.register')->post()->addClass('max-w-md mx-auto') !!}
    <div class="space-y-6">
        {!! PrelineForm::text('username')
            ->label('Username')
            ->placeholder('Enter your username')
            ->required()
            ->hint('Must be unique and at least 3 characters') !!}

        {!! PrelineForm::email('email')
            ->label('Email Address')
            ->placeholder('your@email.com')
            ->required() !!}

        {!! PrelineForm::password('password')
            ->label('Password')
            ->required()
            ->hint('Minimum 8 characters with numbers and symbols') !!}

        {!! PrelineForm::password('password_confirmation')
            ->label('Confirm Password')
            ->required() !!}

        <div class="flex items-center">
            {!! PrelineForm::checkbox('terms', 1)
                ->label('I agree to the Terms of Service')
                ->required() !!}
        </div>

        <div class="pt-4">
            {!! PrelineForm::submit('Create Account')
                ->primary()
                ->addClass('w-full') !!}
        </div>
    </div>
{!! PrelineForm::close() !!}
```

### Product Creation Form

```php
{!! PrelineForm::open('products.store')->post()->multipart() !!}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-4">
            {!! PrelineForm::text('name')
                ->label('Product Name')
                ->required() !!}

            {!! PrelineForm::textarea('description')
                ->label('Description')
                ->rows(4) !!}

            {!! PrelineForm::number('price')
                ->label('Price')
                ->min(0)
                ->step(0.01)
                ->required() !!}
        </div>

        <div class="space-y-4">
            {!! PrelineForm::select('category_id', $categories)
                ->label('Category')
                ->placeholder('Select a category')
                ->required() !!}

            {!! PrelineForm::file('images')
                ->label('Product Images')
                ->multiple()
                ->accept('image/*') !!}

            {!! PrelineForm::checkbox('is_featured', 1)
                ->label('Featured Product') !!}
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        {!! PrelineForm::submit('Save Product')->primary() !!}
        {!! PrelineForm::button('Cancel')->secondary() !!}
    </div>
{!! PrelineForm::close() !!}
```

## 🎨 Styling & Customization

This package uses Tailwind CSS classes for styling. The default styling follows Preline UI design patterns:

### Default Input Styling

- Border: `border-gray-200`
- Focus: `focus:border-blue-500 focus:ring-blue-500`
- Dark mode: `dark:bg-slate-900 dark:border-gray-700`
- Error state: `border-red-500 focus:border-red-500`

### Button Variants

- **Primary**: `bg-blue-600 hover:bg-blue-700 text-white`
- **Secondary**: `bg-white border-gray-300 text-gray-700`
- **Danger**: `bg-red-600 hover:bg-red-700 text-white`
- **Success**: `bg-green-600 hover:bg-green-700 text-white`

### Form Layout Classes

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
<!-- Include Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Include Preline UI CSS -->
<link href="https://preline.co/assets/css/main.min.css" rel="stylesheet" />

<!-- Include Preline UI JavaScript -->
<script src="https://preline.co/assets/js/preline.js"></script>
```

Or install via npm:

```bash
npm install preline tailwindcss
```

### Tailwind Configuration

Add PrelineForm files to your `tailwind.config.js`:

```javascript
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./vendor/laravolt/preline-form/**/*.php",
  ],
  // ... rest of your config
};
```

## 🔍 Troubleshooting

### Common Issues

#### Styles not applying correctly

```bash
# Make sure Tailwind CSS is properly configured
npm install -D tailwindcss
npx tailwindcss init

# Add to your tailwind.config.js
content: [
    "./vendor/laravolt/preline-form/**/*.php",
]

# Rebuild CSS
npm run build
```

#### JavaScript components not working

```html
<!-- Make sure Preline UI JS is loaded -->
<script src="https://preline.co/assets/js/preline.js"></script>

<!-- Initialize components after DOM is ready -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.HSStaticMethods.autoInit();
  });
</script>
```

#### Validation errors not showing

```php
// Ensure error store is configured properly
// This is usually handled automatically by Laravel
```

#### Form submission issues

```php
// Make sure CSRF token is included (automatically handled)
// For custom forms, ensure proper method spoofing
PrelineForm::open('route')->put(); // Adds _method=PUT hidden field
```

### Performance Tips

1. **Optimize Tailwind CSS** - Use PurgeCSS to remove unused styles
2. **Load Preline UI selectively** - Only include components you use
3. **Cache form configurations** - Use Laravel's config caching

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Setup

```bash
git clone https://github.com/laravolt/laravolt
cd packages/preline-form
composer install
```

### Running Tests

```bash
composer test
composer test:coverage
```

## Credits

Preline Form is inspired by and based on [laravolt/semantic-form](https://github.com/laravolt/semantic-form) by [Laravolt](https://laravolt.dev), adapted to use [Preline UI](https://preline.co/) and Tailwind CSS.
