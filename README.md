# Laravolt Form Builders

Configurable form builder system for Laravel that supports both **Semantic UI** and **Preline UI (Tailwind CSS)** frameworks.

## Features

- 🎨 **Multiple UI Frameworks**: Switch between Semantic UI and Preline UI/Tailwind CSS
- 🔄 **Runtime Switching**: Change form builders within the same request
- 🤖 **Auto-Detection**: Automatically detect the best form builder based on your CSS framework
- 🎯 **Unified API**: Same API regardless of the underlying UI framework
- ⚙️ **Easy Configuration**: Simple configuration file to manage form builders
- 🚀 **Artisan Commands**: CLI tools for managing form builders

## Installation

The form builder system is included as part of the Laravolt platform. Both SemanticForm and PrelineForm packages are available.

## Configuration

### Publishing Configuration

```bash
php artisan vendor:publish --tag=form-config
```

This will publish `config/form.php` where you can configure your form builders.

### Environment Configuration

Add to your `.env` file:

```env
# Default form builder (semantic or preline)
FORM_BUILDER=semantic

# Enable auto-detection of best form builder
FORM_AUTO_DETECT=false

# Enable runtime switching between form builders
FORM_RUNTIME_SWITCHING=true
```

## Usage

### Basic Usage (Unified API)

The unified API works with both form builders:

```php
use Laravolt\SemanticForm\UnifiedFacade as Form;

// This will use the configured default form builder
{!! Form::open(route('users.store')) !!}
    {!! Form::text('name')->label('Full Name') !!}
    {!! Form::email('email')->label('Email Address') !!}
    {!! Form::submit('Save User') !!}
{!! Form::close() !!}
```

### Switching Form Builders

#### Method 1: Configuration
Set the default in `config/form.php`:

```php
'default' => 'preline', // or 'semantic'
```

#### Method 2: Environment Variable
```env
FORM_BUILDER=preline
```

#### Method 3: Runtime Switching
```php
// Switch to Preline UI for this section
{!! Form::preline()->open(route('modern.form')) !!}
    {!! Form::text('username')->label('Username') !!}
    {!! Form::password('password')->label('Password') !!}
    {!! Form::submit('Login')->primary() !!}
{!! Form::close() !!}

// Switch back to Semantic UI
{!! Form::semantic()->open(route('classic.form')) !!}
    {!! Form::text('search')->label('Search') !!}
    {!! Form::submit('Search') !!}
{!! Form::close() !!}
```

#### Method 4: Direct Driver Access
```php
// Use specific driver
{!! form('preline')->open(route('tailwind.form')) !!}
{!! form('semantic')->open(route('semantic.form')) !!}
```

### Auto-Detection

Enable auto-detection to automatically choose the best form builder:

```php
// In config/form.php
'auto_detect' => [
    'enabled' => true,
    'detection_method' => 'css_scan', // or 'config'
],
```

The system will automatically detect:
- **Tailwind CSS** → Uses Preline Form
- **Semantic UI** → Uses Semantic Form

## Artisan Commands

### List Available Form Builders
```bash
php artisan form:builder list
```

### Switch Form Builder
```bash
php artisan form:builder switch preline
php artisan form:builder switch semantic
```

### Show Form Builder Information
```bash
php artisan form:builder info
php artisan form:builder info preline
```

### Auto-Detect Best Form Builder
```bash
php artisan form:builder detect
```

### Publish Configuration
```bash
php artisan form:builder list --publish
```

## Form Builders

### 1. Semantic Form
- **UI Framework**: Semantic UI
- **CSS Framework**: Semantic UI CSS
- **Best for**: Applications using Semantic UI, jQuery-based interactions
- **Package**: `packages/semantic-form`

#### Example:
```php
{!! SemanticForm::open() !!}
    {!! SemanticForm::text('username')->label('Username') !!}
    {!! SemanticForm::submit('Login') !!}
{!! SemanticForm::close() !!}
```

### 2. Preline Form  
- **UI Framework**: Preline UI
- **CSS Framework**: Tailwind CSS
- **Best for**: Modern applications using Tailwind CSS, component-based design
- **Package**: `packages/preline-form`

#### Example:
```php
{!! PrelineForm::open() !!}
    {!! PrelineForm::text('username')->label('Username') !!}
    {!! PrelineForm::submit('Login')->primary() !!}
{!! PrelineForm::close() !!}
```

## API Reference

Both form builders support the same API:

### Form Elements
```php
Form::text($name, $value)
Form::email($name, $value)
Form::password($name)
Form::number($name, $value)
Form::textarea($name, $value)
Form::select($name, $options, $selected)
Form::selectMultiple($name, $options, $selected)
Form::checkbox($name, $value, $checked)
Form::radio($name, $value, $checked)
Form::radioGroup($name, $options, $checked)
Form::checkboxGroup($name, $options, $checked)
Form::file($name)
Form::hidden($name, $value)
Form::date($name, $value)
Form::time($name, $value)
Form::color($name, $value)
```

### Buttons
```php
Form::button($text)
Form::submit($text)

// Preline Form additional button styles
Form::submit('Save')->primary()
Form::button('Cancel')->secondary()
Form::submit('Delete')->danger()
Form::submit('Confirm')->success()
```

### Form Methods
```php
Form::open($action, $model)
Form::get($url)
Form::post($url)
Form::put($url)
Form::patch($url)
Form::delete($url)
Form::close()
```

### Model Binding
```php
{!! Form::open(route('users.update', $user), $user) !!}
// or
{!! Form::bind($user)->put(route('users.update', $user)) !!}
```

## Helper Functions

```php
// Get form manager
$manager = form();

// Get specific form builder
$semantic = form('semantic');
$preline = form('preline');

// Direct access to builders
$semantic = semantic_form();
$preline = preline_form();
```

## Form Builder Information

Get information about form builders:

```php
$manager = form();

// Current form builder
$current = $manager->getCurrentDriver(); // 'semantic' or 'preline'

// Available form builders
$available = $manager->getAvailableDrivers(); // ['semantic', 'preline']

// Form builder details
$info = $manager->getBuilderInfo('preline');
/*
[
    'driver' => 'preline',
    'class' => 'Laravolt\PrelineForm\PrelineForm',
    'ui_framework' => 'preline-ui',
    'css_framework' => 'tailwindcss',
    'description' => 'Preline UI form builder with Tailwind CSS styling',
    'is_current' => false
]
*/
```

## Styling Differences

### Semantic Form (Semantic UI)
- Uses Semantic UI CSS classes
- jQuery-based interactions
- Grid system with `ui form` classes
- Semantic color scheme

### Preline Form (Tailwind CSS)
- Uses Tailwind CSS utility classes
- Modern design with Preline UI components
- Flexbox/Grid layout with spacing utilities
- Customizable with Tailwind configuration

## Migration Guide

### From Semantic Form to Preline Form

1. **Switch the default builder**:
   ```env
   FORM_BUILDER=preline
   ```

2. **Update your CSS**: Replace Semantic UI with Tailwind CSS + Preline UI

3. **Test your forms**: The API is identical, but styling will be different

### From Preline Form to Semantic Form

1. **Switch the default builder**:
   ```env
   FORM_BUILDER=semantic
   ```

2. **Update your CSS**: Replace Tailwind CSS with Semantic UI

3. **Remove Preline-specific styling**: Some Preline Form features (like button variants) may not apply

## Advanced Configuration

### Custom Form Builder

You can register custom form builders in `config/form.php`:

```php
'builders' => [
    'custom' => [
        'driver' => 'custom',
        'class' => \App\Forms\CustomFormBuilder::class,
        'facade' => \App\Forms\CustomFacade::class,
        'ui_framework' => 'custom-ui',
        'css_framework' => 'custom-css',
        'description' => 'Custom form builder',
    ],
],
```

### Runtime Configuration

```php
// Check if runtime switching is enabled
if (config('form.runtime_switching.enabled')) {
    $builder = Form::switchTo('preline');
}

// Get form builder information
$info = Form::info();
```

## Troubleshooting

### Form Builder Not Found
- Ensure the form builder package is installed
- Check `config/form.php` configuration
- Verify the driver name is correct

### Styles Not Applied
- **Semantic UI**: Ensure Semantic UI CSS is loaded
- **Preline/Tailwind**: Ensure Tailwind CSS and Preline UI are configured

### Runtime Switching Issues
- Check if `runtime_switching.enabled` is `true` in config
- Clear config cache: `php artisan config:clear`

## Credits

- **Semantic Form**: Based on [Laravolt Semantic Form](https://github.com/laravolt/semantic-form)
- **Preline Form**: Built for [Preline UI](https://preline.co/) with Tailwind CSS
- **Form Manager**: Unified system for managing multiple form builders
