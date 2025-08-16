# Laravolt: Configurable UI & Form Builder System

A comprehensive Laravel platform with configurable UI frameworks and form builders that supports both **Semantic UI** and **Preline UI (Tailwind CSS)** with seamless switching capabilities.

## 🎨 UI Framework Features

- **🔄 Multi-Framework Support**: Switch between Semantic UI and Preline UI
- **🎯 Unified API**: Same codebase works with different UI frameworks
- **🤖 Auto-Detection**: Automatically detect the best UI framework
- **⚙️ Easy Configuration**: Simple configuration files and environment variables
- **🚀 Performance Optimized**: Memory-efficient with caching and lazy loading
- **🛠️ Developer Tools**: Artisan commands for framework management

## 🏗️ Architecture

### UI Framework System
- **UI Manager**: Handles switching between UI frameworks
- **Form Manager**: Manages form builders for different UI frameworks
- **Component Mapping**: Maps components between frameworks for consistency
- **Auto-Detection**: Automatically selects the best framework based on your setup

### Supported UI Frameworks

#### 1. Semantic UI (Default)
- **CSS Framework**: Semantic UI
- **JavaScript**: jQuery-based components
- **Form Builder**: SemanticForm
- **Best for**: Traditional web applications, jQuery-based interactions

#### 2. Preline UI (Ready for Integration)
- **CSS Framework**: Tailwind CSS
- **JavaScript**: Vanilla JS/Alpine.js compatible
- **Form Builder**: PrelineForm  
- **Best for**: Modern applications, utility-first CSS, component-based design

## 📦 Installation & Configuration

### Environment Configuration

```env
# UI Framework Selection
UI_FRAMEWORK=semantic              # or 'preline'
UI_AUTO_DETECT=false              # Enable automatic detection
UI_FONT_SIZE=sm                   # Font size for current framework
UI_THEME=light                    # Theme: light or dark

# Form Builder Configuration  
FORM_BUILDER=semantic             # Matches UI framework
FORM_AUTO_DETECT=false           # Auto-detect form builder
FORM_RUNTIME_SWITCHING=true     # Allow runtime switching

# Framework-Specific Settings
SEMANTIC_UI_ENABLED=true         # Enable Semantic UI
SEMANTIC_THEME=light            # Semantic UI theme
SEMANTIC_BUTTON_COLOR=blue      # Button color scheme

PRELINE_UI_ENABLED=false        # Enable when ready to use Preline UI
PRELINE_THEME=light            # Preline UI theme
PRELINE_COLOR_SCHEME=blue      # Color scheme
```

### Publish Configuration Files

```bash
# Publish UI configuration
php artisan vendor:publish --tag=ui-config

# Publish form configuration  
php artisan vendor:publish --tag=form-config
```

## 🛠️ Usage

### Basic UI Framework Usage

```php
// Get current UI framework
$framework = current_ui_framework(); // 'semantic' or 'preline'

// Check current framework
if (is_semantic_ui()) {
    // Semantic UI specific code
}

if (is_preline_ui()) {
    // Preline UI specific code
}

// Get CSS classes for components
$buttonClass = ui_class('button');
$formClass = ui_class('form', ['additional-class']);

// Use in Blade templates
<form class="{{ ui_class('form') }}">
    <button class="{{ ui_class('button', ['primary']) }}">Submit</button>
</form>
```

### Form Builder Integration

```php
use Laravolt\SemanticForm\UnifiedFacade as Form;

// Unified form API - works with both frameworks
{!! Form::open(route('users.store')) !!}
    {!! Form::text('name')->label('Full Name') !!}
    {!! Form::email('email')->label('Email Address') !!}
    {!! Form::submit('Save User') !!}
{!! Form::close() !!}

// Explicit framework switching
{!! Form::semantic()->open(route('classic.form')) !!}
    {!! Form::text('username')->label('Username') !!}
{!! Form::close() !!}

{!! Form::preline()->open(route('modern.form')) !!}
    {!! Form::text('username')->label('Username') !!}
    {!! Form::submit('Login')->primary() !!}
{!! Form::close() !!}
```

### UI Manager Advanced Usage

```php
$uiManager = app('ui-manager');

// Switch frameworks programmatically
$uiManager->switchTo('preline');

// Get framework information
$info = $uiManager->getFrameworkInfo();
/*
[
    'name' => 'Semantic UI',
    'framework' => 'semantic',
    'css_framework' => 'semantic-ui',
    'js_framework' => 'jquery',
    'form_builder' => 'semantic',
    'enabled' => true,
    'is_current' => true
]
*/

// Auto-detection
$bestFramework = $uiManager->autoDetect();

// Get CSS classes
$containerClass = $uiManager->getCssClass('container');
$customButtonClass = $uiManager->buildCssClass('button', ['large', 'primary']);
```

## 🚀 Artisan Commands

### UI Framework Management

```bash
# List available UI frameworks
php artisan ui:framework list

# Switch to a different framework
php artisan ui:framework switch preline
php artisan ui:framework switch semantic

# Get framework information
php artisan ui:framework info
php artisan ui:framework info preline

# Auto-detect best framework
php artisan ui:framework detect

# Show framework status and statistics
php artisan ui:framework status

# Enable a framework
php artisan ui:framework switch preline --enable
```

### Form Builder Management

```bash
# List available form builders
php artisan form:builder list

# Switch form builders
php artisan form:builder switch preline
php artisan form:builder switch semantic

# Get form builder information
php artisan form:builder info

# Auto-detect best form builder
php artisan form:builder detect
```

## 🎨 Framework Comparison

| Feature | Semantic UI | Preline UI |
|---------|-------------|------------|
| **CSS Approach** | Component-based classes | Utility-first classes |
| **Bundle Size** | Larger (full framework) | Smaller (tree-shakeable) |
| **Customization** | Theme-based | Utility classes |
| **JavaScript** | jQuery required | Vanilla JS/Alpine.js |
| **Learning Curve** | Easier (semantic names) | Steeper (utility classes) |
| **Performance** | Good | Excellent |
| **Modern Features** | Traditional | Cutting-edge |

## 📱 Component Mapping

The system automatically maps components between frameworks:

```php
// config/ui.php - Component Mapping
'component_mapping' => [
    'button' => [
        'semantic' => 'ui button',
        'preline' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md',
    ],
    'input' => [
        'semantic' => 'ui input',
        'preline' => 'block w-full border-gray-300 rounded-md shadow-sm',
    ],
    'form' => [
        'semantic' => 'ui form',
        'preline' => 'space-y-6',
    ],
    'container' => [
        'semantic' => 'ui container',
        'preline' => 'max-w-7xl mx-auto px-4',
    ],
],
```

## 🔧 Configuration Reference

### UI Framework Configuration (`config/ui.php`)

```php
return [
    // Current framework
    'framework' => env('UI_FRAMEWORK', 'semantic'),
    
    // Framework definitions
    'frameworks' => [
        'semantic' => [
            'name' => 'Semantic UI',
            'css_framework' => 'semantic-ui',
            'js_framework' => 'jquery',
            'form_builder' => 'semantic',
            'enabled' => true,
            'settings' => [
                'font_size' => env('SEMANTIC_FONT_SIZE', 'sm'),
                'theme' => env('SEMANTIC_THEME', 'light'),
                'button_color' => env('SEMANTIC_BUTTON_COLOR', 'blue'),
            ],
        ],
        'preline' => [
            'name' => 'Preline UI',
            'css_framework' => 'tailwindcss',
            'js_framework' => 'vanilla',
            'form_builder' => 'preline',
            'enabled' => env('PRELINE_UI_ENABLED', false),
            'settings' => [
                'font_size' => env('PRELINE_FONT_SIZE', 'text-sm'),
                'theme' => env('PRELINE_THEME', 'light'),
                'color_scheme' => env('PRELINE_COLOR_SCHEME', 'blue'),
            ],
        ],
    ],
    
    // Auto-detection settings
    'auto_detect' => [
        'enabled' => env('UI_AUTO_DETECT', false),
        'priority' => ['preline', 'semantic'],
        'detection_methods' => [
            'package_json' => true,
            'css_files' => true,
            'config_hints' => true,
        ],
    ],
];
```

### Form Builder Configuration (`config/form.php`)

```php
return [
    // Default form builder
    'default' => env('FORM_BUILDER', 'semantic'),
    
    // Builder definitions
    'builders' => [
        'semantic' => [
            'driver' => 'semantic',
            'class' => \Laravolt\SemanticForm\SemanticForm::class,
            'ui_framework' => 'semantic-ui',
            'css_framework' => 'semantic-ui',
        ],
        'preline' => [
            'driver' => 'preline',
            'class' => \Laravolt\PrelineForm\PrelineForm::class,
            'ui_framework' => 'preline-ui',
            'css_framework' => 'tailwindcss',
        ],
    ],
    
    // Runtime switching
    'runtime_switching' => [
        'enabled' => env('FORM_RUNTIME_SWITCHING', true),
        'cache_builder_instances' => true,
    ],
];
```

## 🎯 Migration Guide

### From Single Framework to Multi-Framework

1. **Update Environment Variables**:
   ```env
   UI_FRAMEWORK=semantic  # Your current framework
   FORM_BUILDER=semantic  # Matching form builder
   ```

2. **Update Templates** (Optional):
   ```blade
   {{-- Before --}}
   <form class="ui form">
   
   {{-- After (framework-agnostic) --}}
   <form class="{{ ui_class('form') }}">
   ```

3. **Test Different Frameworks**:
   ```bash
   php artisan ui:framework switch preline
   php artisan config:clear
   ```

### Enabling Preline UI (When Ready)

1. **Install Tailwind CSS & Preline UI**:
   ```bash
   npm install -D tailwindcss @tailwindcss/forms
   npm install preline
   ```

2. **Enable in Configuration**:
   ```env
   PRELINE_UI_ENABLED=true
   ```

3. **Switch Framework**:
   ```bash
   php artisan ui:framework switch preline
   ```

## ⚡ Performance Features

- **Memory Optimization**: Compiled configuration caching
- **CSS Class Caching**: Cached component-to-CSS mappings
- **Lazy Loading**: Framework detection results cached
- **Minimal File Operations**: Optimized for in-memory deployments
- **Static Analysis**: Pre-compiled configurations for production

## 🔍 Auto-Detection

The system can automatically detect the best UI framework based on:

1. **Package.json Analysis**: Checks for framework dependencies
2. **CSS File Detection**: Scans for framework-specific CSS files
3. **Configuration Hints**: Uses existing configuration settings
4. **Priority Order**: Configurable framework preference

## 🎁 Preline UI Integration (Future)

Currently, the system is fully prepared for Preline UI integration:

- ✅ **Infrastructure Ready**: All systems support Preline UI
- ✅ **Configuration Complete**: Preline UI fully configured
- ✅ **Form Builder**: PrelineForm package created and ready
- ✅ **Component Mapping**: CSS class mappings defined
- ⏳ **Integration Pending**: Awaiting Preline UI discussion/implementation

To enable Preline UI when ready:
```env
PRELINE_UI_ENABLED=true
UI_FRAMEWORK=preline
```

## 🤝 Contributing

This system is designed to be extensible. To add new UI frameworks:

1. **Define Framework Configuration** in `config/ui.php`
2. **Create Form Builder** (if needed)
3. **Add Component Mappings**
4. **Update Detection Logic**
5. **Test Integration**

## 📚 Documentation

- **Form Builder API**: Same API for all frameworks
- **UI Framework Guide**: Switching and configuration
- **Component Reference**: Available components and mappings
- **Performance Guide**: Optimization for production
- **Migration Guide**: Moving between frameworks

This configurable system provides the foundation for supporting multiple UI frameworks while maintaining a consistent developer experience and allowing for future expansion to additional frameworks as needed.
