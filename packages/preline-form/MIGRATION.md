# Migration Guide: SemanticForm → PrelineForm

This guide helps you migrate from SemanticForm to PrelineForm smoothly.

## 🔄 API Compatibility

PrelineForm maintains **100% API compatibility** with SemanticForm for core methods:

| SemanticForm     | PrelineForm           | Status        | Notes                    |
| ---------------- | --------------------- | ------------- | ------------------------ |
| `Form::text()`   | `PrelineForm::text()` | ✅ Compatible | Same method signature    |
| `->attributes()` | `->attributes()`      | ✅ Compatible | Set multiple attributes  |
| `->horizontal()` | `->horizontal()`      | ✅ Compatible | Horizontal form layout   |
| `->make()`       | `->make()`            | ✅ Compatible | Dynamic field generation |
| `->hasError()`   | `->hasError()`        | ✅ Compatible | Check validation errors  |
| `->getError()`   | `->getError()`        | ✅ Compatible | Get error messages       |
| `->bind()`       | `->bind()`            | ✅ Compatible | Model binding            |
| `->setChecked()` | `->setChecked()`      | ✅ Compatible | Checkbox/radio state     |

## 📋 Step-by-Step Migration

### 1. Update Dependencies

```bash
# Remove SemanticForm
composer remove laravolt/semantic-form

# Install PrelineForm
composer require laravolt/preline-form
```

### 2. Update Imports and Facades

```php
// Before (SemanticForm)
use Laravolt\SemanticForm\Facade as Form;

// After (PrelineForm)
use Laravolt\PrelineForm\Facade as PrelineForm;

// Or use the helper function
// form() becomes form()
```

### 3. Update CSS Framework

Remove Semantic UI and add Tailwind CSS + Preline UI:

```html
<!-- Remove Semantic UI -->
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css"
/>
<script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>

<!-- Add Tailwind CSS + Preline UI -->
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://preline.co/assets/css/main.min.css" rel="stylesheet" />
<script src="https://preline.co/assets/js/preline.js"></script>
```

### 4. Update Blade Templates

Most of your existing code will work without changes:

```php
// This works in both SemanticForm and PrelineForm
{!! PrelineForm::open('user.store')->post() !!}
    {!! PrelineForm::text('username')->label('Username')->required() !!}
    {!! PrelineForm::email('email')->label('Email')->required() !!}
    {!! PrelineForm::submit('Save')->primary() !!}
{!! PrelineForm::close() !!}
```

### 5. Update Custom Styling

Replace Semantic UI classes with Tailwind CSS equivalents:

```php
// Before (Semantic UI)
Form::text('username')->addClass('ui large input')

// After (Tailwind CSS)
PrelineForm::text('username')->addClass('text-lg p-4')
```

## 🎨 Styling Differences

### Form Elements

| Element          | SemanticForm (Semantic UI) | PrelineForm (Tailwind)       |
| ---------------- | -------------------------- | ---------------------------- |
| Text Input       | `ui input`                 | `border-gray-200 rounded-lg` |
| Button Primary   | `ui primary button`        | `bg-blue-600 text-white`     |
| Button Secondary | `ui button`                | `bg-white border-gray-300`   |
| Error State      | `ui error input`           | `border-red-500`             |
| Form Layout      | `ui form`                  | `space-y-6`                  |

### Color Scheme

| SemanticForm | PrelineForm   | Tailwind Equivalent |
| ------------ | ------------- | ------------------- |
| `primary`    | `primary()`   | `bg-blue-600`       |
| `secondary`  | `secondary()` | `bg-gray-600`       |
| `positive`   | `success()`   | `bg-green-600`      |
| `negative`   | `danger()`    | `bg-red-600`        |

## 🔧 Advanced Migration

### Custom Form Elements

If you've extended SemanticForm elements:

```php
// Before (SemanticForm)
class CustomText extends \Laravolt\SemanticForm\Elements\Text
{
    protected function setDefaultClasses()
    {
        $this->addClass('ui large input');
    }
}

// After (PrelineForm)
class CustomText extends \Laravolt\PrelineForm\Elements\Text
{
    protected function setDefaultClasses()
    {
        $this->addClass('text-lg p-4 border-gray-200 rounded-lg');
    }
}
```

### Service Provider Configuration

Update your service provider if you've customized form configuration:

```php
// Before
$this->app->bind('semantic-form', function () {
    return new SemanticForm($config);
});

// After
$this->app->bind('preline-form', function () {
    return new PrelineForm($config);
});
```

## ⚠️ Breaking Changes

### Minimal Breaking Changes

PrelineForm is designed to minimize breaking changes:

1. **CSS Classes**: Default styling uses Tailwind instead of Semantic UI
2. **JavaScript**: Preline UI components instead of Semantic UI
3. **Facade Name**: `Form::` becomes `PrelineForm::`

### No Breaking Changes in API

- ✅ All method names remain the same
- ✅ Method signatures are identical
- ✅ Chaining patterns work exactly the same
- ✅ Error handling works the same way

## 🧪 Testing Your Migration

### 1. Visual Testing

Compare forms side-by-side:

```php
// Test basic form elements
PrelineForm::text('test')->label('Test Field')
PrelineForm::select('options', ['a' => 'Option A'])->label('Select')
PrelineForm::checkbox('check', 1)->label('Checkbox')
```

### 2. Functionality Testing

Ensure all form features work:

```php
// Test validation errors
PrelineForm::text('email')->label('Email') // Should show errors when validation fails

// Test model binding
PrelineForm::bind($user)->text('name')->label('Name')

// Test dynamic fields
PrelineForm::make([
    'name' => ['type' => 'text', 'label' => 'Name']
])
```

### 3. Browser Testing

- ✅ Test in different browsers
- ✅ Test responsive design
- ✅ Test dark mode (if used)
- ✅ Test JavaScript interactions

## 🚀 Post-Migration Optimization

### 1. Optimize Tailwind CSS

```javascript
// tailwind.config.js
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./vendor/laravolt/preline-form/**/*.php",
  ],
  // Remove unused styles in production
};
```

### 2. Customize Default Styling

```php
// Override default classes globally
PrelineForm::macro('customText', function ($name) {
    return PrelineForm::text($name)->addClass('border-purple-500');
});
```

### 3. Performance Improvements

- Use Tailwind's purge feature to reduce CSS size
- Load only necessary Preline UI components
- Optimize asset loading

## 🆘 Need Help?

If you encounter issues during migration:

1. Check the [troubleshooting section](README.md#troubleshooting) in the README
2. Compare your code with the [examples](README.md#complete-examples)
3. Create an issue on GitHub with your specific migration problem

## 📈 Migration Checklist

- [ ] Updated composer dependencies
- [ ] Updated imports and facades
- [ ] Replaced CSS frameworks (Semantic UI → Tailwind + Preline)
- [ ] Updated custom styling to use Tailwind classes
- [ ] Tested form functionality
- [ ] Tested validation and error handling
- [ ] Tested responsive design
- [ ] Optimized asset loading
- [ ] Updated documentation/comments

Your migration to PrelineForm should be smooth and straightforward thanks to the maintained API compatibility!
