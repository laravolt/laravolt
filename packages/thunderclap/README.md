# Laravolt Thunderclap

⚡ **Lightning-fast CRUD generator for Laravel** - Generate complete, production-ready modules from database tables in seconds.

[![Latest Version](https://img.shields.io/packagist/v/laravolt/thunderclap.svg)](https://packagist.org/packages/laravolt/thunderclap)
[![Total Downloads](https://img.shields.io/packagist/dt/laravolt/thunderclap.svg)](https://packagist.org/packages/laravolt/thunderclap)
[![License](https://img.shields.io/packagist/l/laravolt/thunderclap.svg)](https://packagist.org/packages/laravolt/thunderclap)

**✨ Smart Generation** • **🔍 Model Detection** • **🎨 Preline UI** • **🔄 Auto-Enhancement** • **📦 Modular Architecture**

## 🚀 What is Thunderclap?

Thunderclap is an intelligent code generator that creates complete CRUD modules from your database tables. It analyzes your database schema and generates:

- 📋 **Models** with automatic traits (AutoFilter, AutoSearch, AutoSort)
- 🎮 **Controllers** with full CRUD operations
- 👁️ **Views** using modern Preline UI and Tailwind CSS
- ✅ **Form Requests** with validation rules
- 🧪 **Tests** with factories and test cases
- 🗺️ **Routes** with proper middleware and naming
- 📦 **Service Providers** with menu registration

### Key Features

- ⚡ **Instant CRUD Generation** - Complete module in seconds
- 🧠 **Smart Model Detection** - Auto-discovers existing models
- 🔄 **Model Enhancement** - Adds required traits and properties to existing models
- 🎨 **Modern UI** - Uses Preline UI components with Tailwind CSS
- 📦 **Modular Structure** - Generates organized, self-contained modules
- 🔍 **Multiple Templates** - Customizable stub templates
- 🎯 **Type-Safe** - PHP 8.2+ with strict types
- 🧪 **Test-Ready** - Includes factories and test cases

## 📋 Table of Contents

- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Core Concepts](#-core-concepts)
- [Commands](#-commands)
- [Generated Module Structure](#-generated-module-structure)
- [Configuration](#-configuration)
- [Advanced Usage](#-advanced-usage)
- [Templates & Customization](#-templates--customization)
- [Model Enhancement](#-model-enhancement)
- [Best Practices](#-best-practices)
- [Troubleshooting](#-troubleshooting)

## 📦 Installation

Thunderclap is included with Laravolt by default. If you need to install it separately:

```bash
composer require laravolt/thunderclap
```

### Requirements

| Requirement | Version |
|------------|---------|
| **PHP** | `>= 8.2` |
| **Laravel** | `^10.0 \|\| ^11.0 \|\| ^12.0` |
| **Doctrine DBAL** | `^3.0` |
| **Laravolt Suitable** | For AutoFilter, AutoSearch, AutoSort traits |

### Optional Dependencies

```bash
# For code formatting (recommended)
composer require laravel/pint --dev
```

## 🚀 Quick Start

### 1. Basic CRUD Generation

Generate a complete CRUD module from a database table:

```bash
# Interactive mode - choose from available tables
php artisan laravolt:clap

# Generate from specific table
php artisan laravolt:clap --table=users

# Generate with custom module name
php artisan laravolt:clap --table=users --module=UserManagement
```

**What gets generated:**

```
modules/
└── User/
    ├── Controllers/
    │   └── UserController.php
    ├── Models/
    │   ├── User.php
    │   └── UserFactory.php
    ├── Requests/
    │   ├── Store.php
    │   └── Update.php
    ├── Tests/
    │   └── UserTest.php
    ├── resources/
    │   └── views/
    │       ├── index.blade.php
    │       ├── create.blade.php
    │       ├── edit.blade.php
    │       ├── show.blade.php
    │       └── _form.blade.php
    ├── routes/
    │   └── web.php
    ├── config/
    │   └── user.php
    ├── ServiceProvider.php
    └── UserTableView.php
```

### 2. Using Existing Models

If you already have models in `app/Models`, Thunderclap will detect them:

```bash
# List all models and their status
php artisan laravolt:models

# Generate CRUD and auto-enhance existing model
php artisan laravolt:clap --table=users --use-existing-models

# Check specific model
php artisan laravolt:models --table=users
```

### 3. Generated Code Example

**Controller** (`UserController.php`):
```php
class UserController
{
    public function index(): View
    {
        return view('user::index');
    }

    public function create(): View
    {
        return view('user::create');
    }

    public function store(Store $request): RedirectResponse
    {
        User::create($request->validated());
        return to_route('modules::user.index')->withSuccess('User saved');
    }

    public function show(User $user): View
    {
        return view('user::show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('user::edit', compact('user'));
    }

    public function update(Update $request, User $user): RedirectResponse
    {
        $user->update($request->validated());
        return to_route('modules::user.index')->withSuccess('User updated');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return to_route('modules::user.index')->withSuccess('User deleted');
    }
}
```

**Model** (`User.php`):
```php
class User extends Model
{
    use AutoFilter, AutoSearch, AutoSort, HasFactory;

    protected $table = 'users';
    
    protected $guarded = [];

    /** @var array<string> */
    protected $searchableColumns = ["name", "email", "username"];

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
```

**View** (`index.blade.php`):
```blade
<x-volt-app title="{{ __('User') }}" :isShowTitleBar="false">
    <div class="flex justify-between items-center gap-x-3">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-neutral-200">
            {{ __('User') }}
        </h1>

        <div class="flex justify-end items-center gap-x-2">
            <x-volt-link-button 
                icon="plus" 
                :url="route('modules::user.create')" 
                :label="__('laravolt::action.add')" />
        </div>
    </div>

    @livewire(\Modules\User\UserTableView::class)
</x-volt-app>
```

## 🎯 Core Concepts

### 1. Module-Based Architecture

Thunderclap generates **self-contained modules** in the `modules/` directory:

```
modules/
├── Product/          # Product module
│   ├── Controllers/
│   ├── Models/
│   ├── resources/
│   └── ServiceProvider.php
└── Category/         # Category module
    ├── Controllers/
    ├── Models/
    ├── resources/
    └── ServiceProvider.php
```

Each module is:
- ✅ Self-contained and independent
- ✅ Easy to understand and maintain
- ✅ Simple to move or share
- ✅ Follows consistent structure

### 2. Smart Model Detection

Thunderclap intelligently detects existing models:

```bash
# Detects models in app/Models/
php artisan laravolt:models

# Output:
# ┌─────────┬──────────────┬─────────┬─────────────┬────────────┐
# │ Model   │ Class        │ Table   │ Auto Traits │ Searchable │
# ├─────────┼──────────────┼─────────┼─────────────┼────────────┤
# │ User    │ App\Models\… │ users   │ ❌          │ ❌         │
# │ Product │ App\Models\… │ products│ ✅          │ ✅         │
# └─────────┴──────────────┴─────────┴─────────────┴────────────┘
```

**Detection Logic:**
1. Checks for model in `app/Models/{ModelName}.php`
2. Verifies model uses the correct table
3. Checks for required traits (AutoFilter, AutoSearch, AutoSort)
4. Checks for `$searchableColumns` property
5. Suggests enhancements if needed

### 3. Automatic Model Enhancement

Thunderclap can enhance existing models:

```php
// Before Enhancement
class User extends Model
{
    protected $table = 'users';
}

// After Enhancement (automatic)
class User extends Model
{
    use AutoFilter, AutoSearch, AutoSort, HasFactory;
    
    protected $table = 'users';
    
    /** @var array<string> */
    protected $searchableColumns = ["name", "email"];
    
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
```

**Enhancement includes:**
- ✅ Required traits (AutoFilter, AutoSearch, AutoSort)
- ✅ HasFactory trait
- ✅ Searchable columns based on table schema
- ✅ Factory method
- ✅ Automatic backup before changes

### 4. Schema Introspection

Thunderclap analyzes your database schema using Doctrine DBAL:

**Detected Information:**
- Column names and types
- Required fields (NOT NULL)
- Text vs numeric fields
- Date/time fields
- Foreign key relationships (ending with `_id`)

**Smart Generation:**
- Text fields → `<input type="text">`
- Email fields → `<input type="email">`
- Text columns → `<textarea>`
- Date columns → `<input type="date">`
- DateTime columns → Date picker
- Required fields → `required` validation rule

## 🎮 Commands

### `laravolt:clap` - Generate CRUD Module

Main command for generating CRUD modules from database tables.

#### Syntax

```bash
php artisan laravolt:clap [options]
```

#### Options

| Option | Description | Example |
|--------|-------------|---------|
| `--table=` | Specify table name | `--table=users` |
| `--module=` | Custom module name | `--module=UserManagement` |
| `--template=` | Template to use | `--template=custom` |
| `--force` | Overwrite existing files | `--force` |
| `--use-existing-models` | Auto-enhance existing models | `--use-existing-models` |

#### Interactive Mode

When run without options, launches interactive mode:

```bash
php artisan laravolt:clap

# Step 1: Choose table
Choose table:
  [0] categories
  [1] products
  [2] users
> 2

# Step 2: Existing model detected
⚠️  Existing model detected: App\Models\User

How would you like to proceed?
  [enhance] Enhance existing model
  [create] Create new model in module
  [skip] Skip model generation
> enhance

# Step 3: Generation
Creating modules directory...
Generating code from /path/to/stubs to /path/to/modules/User
✓ Running code style fix...
✓ Successfully enhanced existing model

🎉 Module generation completed!
```

#### Examples

```bash
# Basic generation (interactive)
php artisan laravolt:clap

# Generate from specific table
php artisan laravolt:clap --table=products

# Use existing model automatically
php artisan laravolt:clap --table=users --use-existing-models

# Custom module name
php artisan laravolt:clap --table=user_profiles --module=Profile

# Force overwrite
php artisan laravolt:clap --table=products --force

# Custom template
php artisan laravolt:clap --table=products --template=api
```

### `laravolt:models` - List Models

List all models and their enhancement status.

#### Syntax

```bash
php artisan laravolt:models [options]
```

#### Options

| Option | Description | Example |
|--------|-------------|---------|
| `--table=` | Show model for specific table | `--table=users` |

#### Examples

```bash
# List all models
php artisan laravolt:models

# Output:
# Found 5 model(s):
# 
# ┌──────────┬──────────────────┬──────────┬─────────────┬────────────┐
# │ Model    │ Class            │ Table    │ Auto Traits │ Searchable │
# ├──────────┼──────────────────┼──────────┼─────────────┼────────────┤
# │ User     │ App\Models\User  │ users    │ ✅          │ ✅         │
# │ Product  │ App\Models\Pro…  │ products │ ❌          │ ❌         │
# │ Category │ App\Models\Cat…  │ categor… │ ✅          │ ❌         │
# └──────────┴──────────────────┴──────────┴─────────────┴────────────┘
# 
# Legend:
#   Auto Traits: AutoFilter, AutoSearch, AutoSort
#   Searchable: Has $searchableColumns property

# Check specific table
php artisan laravolt:models --table=users

# Output:
# ✓ Model found: App\Models\User
#   Path: /path/to/app/Models/User.php
#   Table: users
# 
# Enhancement Status:
#   ✓ All required traits present
#   ✓ Has searchableColumns property
```

## 📦 Generated Module Structure

### Complete Module Anatomy

```
modules/Product/
│
├── Controllers/
│   └── ProductController.php       # Full CRUD controller
│
├── Models/
│   ├── Product.php                 # Eloquent model with traits
│   └── ProductFactory.php          # Model factory for testing
│
├── Requests/
│   ├── Store.php                   # Validation for create
│   └── Update.php                  # Validation for update
│
├── Tables/
│   └── ProductTableView.php        # Livewire table component
│
├── Tests/
│   └── ProductTest.php             # Feature tests
│
├── resources/
│   └── views/
│       ├── index.blade.php         # List view with table
│       ├── create.blade.php        # Create form
│       ├── edit.blade.php          # Edit form
│       ├── show.blade.php          # Detail view
│       └── _form.blade.php         # Shared form fields
│
├── routes/
│   └── web.php                     # Module routes
│
├── config/
│   └── product.php                 # Module configuration
│
├── ProductServiceProvider.php      # Service provider
└── ProductTableView.php           # Alternative: TableView class
```

### File Descriptions

#### Controllers

**`ProductController.php`**
```php
namespace Modules\Product\Controllers;

class ProductController
{
    // Standard RESTful methods:
    public function index(): View           // List all products
    public function create(): View          // Show create form
    public function store(Store $request): RedirectResponse
    public function show(Product $product): View
    public function edit(Product $product): View
    public function update(Update $request, Product $product): RedirectResponse
    public function destroy(Product $product): RedirectResponse
}
```

#### Models

**`Product.php`**
```php
namespace Modules\Product\Models;

use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;

class Product extends Model
{
    use AutoFilter, AutoSearch, AutoSort, HasFactory;
    
    protected $table = 'products';
    protected $guarded = [];
    
    /** @var array<string> */
    protected $searchableColumns = ["name", "description", "sku"];
    
    protected static function newFactory()
    {
        return ProductFactory::new();
    }
}
```

**`ProductFactory.php`**
```php
namespace Modules\Product\Models;

class ProductFactory extends Factory
{
    protected $model = Product::class;
    
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'sku' => $this->faker->unique()->slug(),
        ];
    }
}
```

#### Form Requests

**`Store.php`**
```php
namespace Modules\Product\Requests;

class Store extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sku' => ['required', 'string', 'unique:products,sku'],
        ];
    }
}
```

#### Views

**`index.blade.php`** - List view with Livewire table
```blade
<x-volt-app title="Products" :isShowTitleBar="false">
    <div class="flex justify-between items-center gap-x-3">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-neutral-200">
            Products
        </h1>
        <div class="flex justify-end items-center gap-x-2">
            <x-volt-link-button 
                icon="plus" 
                :url="route('modules::product.create')" 
                :label="__('laravolt::action.add')" />
        </div>
    </div>

    @livewire(\Modules\Product\ProductTableView::class)
</x-volt-app>
```

**`_form.blade.php`** - Shared form fields
```blade
{!! form()->text('name')->label('Name')->required() !!}
{!! form()->textarea('description')->label('Description')->required() !!}
{!! form()->number('price')->label('Price')->required() !!}
{!! form()->text('sku')->label('SKU')->required() !!}
```

#### TableView

**`ProductTableView.php`** - Livewire table component
```php
namespace Modules\Product;

use Laravolt\Ui\TableView;

class ProductTableView extends TableView
{
    public function data(): Builder
    {
        return Product::query()
            ->autoSort($this->sortPayload())
            ->autoSearch(trim($this->search))
            ->latest();
    }

    public function columns(): array
    {
        return [
            Numbering::make('No'),
            Text::make('name')->sortable(),
            Text::make('sku')->sortable(),
            Text::make('price')->sortable(),
            RestfulButton::make('modules::product'),
        ];
    }
}
```

#### Service Provider

**`ProductServiceProvider.php`**
```php
namespace Modules\Product;

use Laravolt\Support\Base\BaseServiceProvider;

class ProductServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();
        Livewire::component('modules.product.product-table-view', ProductTableView::class);
    }

    public function getIdentifier(): string
    {
        return 'product';
    }

    protected function menu(): void
    {
        app('laravolt.menu.builder')->register(function ($menu) {
            if ($menu->modules) {
                $menu->modules
                    ->add('Product', route('modules::product.index'))
                    ->data('icon', 'box')
                    ->active('modules/product/*');
            }
        });
    }
}
```

#### Routes

**`routes/web.php`**
```php
use Modules\Product\Controllers\ProductController;

Route::group([
    'prefix' => 'modules/product',
    'as' => 'modules::product.',
], function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
});
```

## ⚙️ Configuration

### Publishing Configuration

```bash
php artisan vendor:publish --provider="Laravolt\Thunderclap\ServiceProvider" --tag="config"
```

This creates `config/laravolt/thunderclap.php`.

### Configuration Options

```php
// config/laravolt/thunderclap.php

return [
    // Columns to exclude from generation
    'columns' => [
        'except' => ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token'],
    ],

    // View configuration
    'view' => [
        'extends' => 'layout',  // Base layout to extend
    ],

    // Route configuration
    'routes' => [
        'prefix' => 'modules::',      // Route name prefix
        'middleware' => [],           // Default middleware
    ],

    // Module configuration
    'namespace' => 'Modules',         // Root namespace for modules
    'target_dir' => base_path('modules'),  // Where to generate modules

    // Transformer (customizes code generation)
    'transformer' => Laravolt\Thunderclap\LaravoltTransformer::class,

    // Files that need module name prefix
    'prefixed' => [
        'ServiceProvider.php',
        'Controller.php',
        'TableView.php',
        'Resource.php',
    ],

    // Default template
    'default' => 'laravolt',

    // Available templates
    'templates' => [
        'laravolt' => 'laravolt',
        // Add custom templates here
    ],
];
```

### Configuration Customization

#### 1. Change Module Directory

```php
'target_dir' => base_path('app/Modules'),
```

#### 2. Add Custom Middleware

```php
'routes' => [
    'prefix' => 'admin::',
    'middleware' => ['auth', 'admin'],
],
```

#### 3. Exclude Additional Columns

```php
'columns' => [
    'except' => [
        'id', 
        'created_at', 
        'updated_at', 
        'deleted_at',
        'remember_token',
        'email_verified_at',  // Add custom exclusions
        'password',
    ],
],
```

#### 4. Custom Namespace

```php
'namespace' => 'App\\Modules',
```

## 🎨 Advanced Usage

### Custom Module Name

Generate module with a specific name:

```bash
# Table: user_profiles
# Module: Profile (instead of UserProfile)
php artisan laravolt:clap --table=user_profiles --module=Profile
```

### Force Overwrite

Overwrite existing module without confirmation:

```bash
php artisan laravolt:clap --table=products --force
```

### Using Existing Models

#### Auto-Enhance Mode

Automatically enhance existing models without prompts:

```bash
php artisan laravolt:clap --table=users --use-existing-models
```

This will:
1. Detect the existing `App\Models\User`
2. Automatically enhance it with required traits
3. Add searchable columns
4. Generate module files
5. Use the existing model instead of creating a new one

#### Manual Enhancement Choice

Without `--use-existing-models`, you'll get a choice menu:

```bash
php artisan laravolt:clap --table=users

# Prompts:
⚠️  Existing model detected: App\Models\User

How would you like to proceed?
  [enhance] Enhance existing model
  [create] Create new model in module
  [skip] Skip model generation
```

**Options:**
- **enhance**: Adds traits and properties to existing model
- **create**: Creates new model in module directory
- **skip**: Uses existing model as-is, no changes

### Working with Foreign Keys

Thunderclap handles foreign keys intelligently:

**Database Schema:**
```sql
CREATE TABLE products (
    id BIGINT PRIMARY KEY,
    category_id BIGINT,        -- Foreign key detected
    name VARCHAR(255),
    description TEXT,
    price DECIMAL(10, 2),
    created_at TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

**Generated Code:**

Foreign keys (ending with `_id`) are:
- ✅ Excluded from searchable columns
- ✅ Excluded from detail views
- ✅ Excluded from factory generation
- ✅ Can be included in forms manually

### Multiple Modules

Generate multiple modules in one workflow:

```bash
# Generate for multiple tables
php artisan laravolt:clap --table=categories
php artisan laravolt:clap --table=products  
php artisan laravolt:clap --table=orders

# Result:
modules/
├── Category/
├── Product/
└── Order/
```

### Custom Transformers

Create custom transformer for specialized generation:

```php
// app/Transformers/ApiTransformer.php
namespace App\Transformers;

use Laravolt\Thunderclap\LaravoltTransformer;

class ApiTransformer extends LaravoltTransformer
{
    public function toApiResource()
    {
        // Custom API resource generation
        return $this->columns
            ->map(function ($column) {
                return "'{$column['name']}' => \$this->{$column['name']},";
            })
            ->implode("\n");
    }
    
    // Override other methods as needed
}
```

Update config:

```php
// config/laravolt/thunderclap.php
'transformer' => App\Transformers\ApiTransformer::class,
```

## 🎨 Templates & Customization

### Understanding Templates

Templates are stub directories containing file templates used for code generation.

**Default Template Location:**
```
packages/thunderclap/stubs/laravolt/
```

### Creating Custom Templates

#### 1. Create Template Directory

```bash
mkdir -p resources/stubs/my-template
```

#### 2. Copy Base Template

```bash
cp -r vendor/laravolt/thunderclap/stubs/laravolt/* resources/stubs/my-template/
```

#### 3. Customize Stubs

Edit stub files in `resources/stubs/my-template/`. Use placeholders:

**Available Placeholders:**

| Placeholder | Description | Example |
|------------|-------------|---------|
| `:Namespace:` | Root namespace | `Modules` |
| `:ModuleName:` | Module name | `Product` |
| `:moduleName:` | camelCase module | `product` |
| `:module-name:` | kebab-case module | `product` |
| `:Module Name:` | Space-separated | `Product` |
| `:table:` | Table name | `products` |
| `:SEARCHABLE_COLUMNS:` | Searchable columns | `"name", "sku"` |
| `:VALIDATION_RULES:` | Validation rules | Generated rules |
| `:TABLE_HEADERS:` | Table headers | HTML headers |
| `:TABLE_FIELDS:` | Table fields | HTML fields |
| `:FORM_CREATE_FIELDS:` | Create form fields | Form inputs |
| `:FORM_EDIT_FIELDS:` | Edit form fields | Form inputs |
| `:DETAIL_FIELDS:` | Detail fields | Display fields |
| `:route-prefix:` | Route prefix | `modules::` |
| `:route-url-prefix:` | URL prefix | `modules/product` |

#### 4. Register Template

```php
// config/laravolt/thunderclap.php
'templates' => [
    'laravolt' => 'laravolt',
    'my-template' => resource_path('stubs/my-template'),
],
```

#### 5. Use Custom Template

```bash
php artisan laravolt:clap --table=products --template=my-template
```

### Example: API-Only Template

Create an API-only template without views:

**Structure:**
```
resources/stubs/api-template/
├── Controllers/
│   └── Controller.php.stub
├── Models/
│   └── Model.php.stub
├── Requests/
│   ├── Store.php.stub
│   └── Update.php.stub
├── Resources/
│   └── Resource.php.stub
└── routes/
    └── api.php.stub
```

**Controller.php.stub:**
```php
<?php

namespace :Namespace:\:ModuleName:\Controllers;

use Illuminate\Http\JsonResponse;
use :Namespace:\:ModuleName:\Models\:ModuleName:;
use :Namespace:\:ModuleName:\Requests\Store;
use :Namespace:\:ModuleName:\Requests\Update;
use :Namespace:\:ModuleName:\Resources\:ModuleName:Resource;

class :ModuleName:Controller
{
    public function index(): JsonResponse
    {
        $data = :ModuleName:::paginate();
        return :ModuleName:Resource::collection($data)->response();
    }

    public function store(Store $request): JsonResponse
    {
        $model = :ModuleName:::create($request->validated());
        return :ModuleName:Resource::make($model)
            ->response()
            ->setStatusCode(201);
    }

    public function show(:ModuleName: $:moduleName:): JsonResponse
    {
        return :ModuleName:Resource::make($:moduleName:)->response();
    }

    public function update(Update $request, :ModuleName: $:moduleName:): JsonResponse
    {
        $:moduleName:->update($request->validated());
        return :ModuleName:Resource::make($:moduleName:)->response();
    }

    public function destroy(:ModuleName: $:moduleName:): JsonResponse
    {
        $:moduleName:->delete();
        return response()->json(null, 204);
    }
}
```

**Usage:**
```bash
php artisan laravolt:clap --table=products --template=api-template
```

## 🔄 Model Enhancement

### What is Model Enhancement?

Model enhancement adds required traits and properties to existing models, making them compatible with Laravolt's features.

### Required Traits

```php
use Laravolt\Suitable\AutoFilter;  // Automatic filtering
use Laravolt\Suitable\AutoSearch;  // Automatic search
use Laravolt\Suitable\AutoSort;    // Automatic sorting
```

### Enhancement Process

#### 1. Check Enhancement Status

```bash
php artisan laravolt:models --table=users
```

**Output:**
```
✓ Model found: App\Models\User
  Path: /path/to/app/Models/User.php
  Table: users

Enhancement Status:
  ❌ Missing traits:
    - AutoFilter
    - AutoSearch
    - AutoSort
  ❌ Missing searchableColumns property

To enhance this model, run:
  php artisan laravolt:clap --table=users --use-existing-models
```

#### 2. Automatic Enhancement

```bash
php artisan laravolt:clap --table=users --use-existing-models
```

**What Happens:**

1. **Backup Created**: `User.php.backup.20240108173221`
2. **Traits Added**: AutoFilter, AutoSearch, AutoSort
3. **Property Added**: `$searchableColumns`
4. **Code Formatted**: Using Laravel Pint
5. **Backup Removed**: If successful

**Before:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
```

**After:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;

class User extends Model
{
    use AutoFilter, AutoSearch, AutoSort;
    
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /** @var array<string> */
    protected $searchableColumns = ["name", "email"];
}
```

#### 3. Manual Enhancement Choice

If you run without `--use-existing-models`:

```bash
php artisan laravolt:clap --table=users

# Choose enhancement method:
How would you like to proceed?
  [enhance] Enhance existing model
  [create] Create new model in module
  [skip] Skip model generation
```

### Enhancement Safety

**Backup System:**
- ✅ Automatic backup before enhancement
- ✅ Timestamped backup file
- ✅ Automatic rollback on error
- ✅ Backup removed on success

**Error Handling:**
```bash
# If enhancement fails
✗ Error enhancing model: Syntax error in file
Restoring from backup...
✓ Backup restored
```

### Searchable Columns Detection

Searchable columns are automatically detected from table schema:

**Rules:**
- ✅ String/text columns included
- ✅ Email columns included
- ❌ ID columns excluded
- ❌ Foreign keys (ending with `_id`) excluded
- ❌ Timestamp columns excluded
- ❌ Password fields excluded

**Example:**

**Table: `users`**
```sql
- id                 (excluded: ID)
- name               (✓ included)
- email              (✓ included)
- username           (✓ included)
- password           (excluded: password)
- role_id            (excluded: foreign key)
- created_at         (excluded: timestamp)
- updated_at         (excluded: timestamp)
```

**Result:**
```php
protected $searchableColumns = ["name", "email", "username"];
```

## 💡 Best Practices

### 1. Database First Approach

Design your database schema first, then generate:

```sql
-- 1. Create migrations
CREATE TABLE products (
    id BIGINT PRIMARY KEY,
    category_id BIGINT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    sku VARCHAR(100) UNIQUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 2. Run migrations
php artisan migrate

-- 3. Generate CRUD
php artisan laravolt:clap --table=products
```

### 2. Use Model Detection

Always check for existing models first:

```bash
# Before generation
php artisan laravolt:models

# If model exists
php artisan laravolt:clap --table=users --use-existing-models

# If no model exists
php artisan laravolt:clap --table=products
```

### 3. Code Review After Generation

Generated code is a starting point. Review and customize:

```php
// 1. Review validation rules
// Requests/Store.php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0'],
        // Add custom rules
        'sku' => ['required', 'unique:products,sku'],
    ];
}

// 2. Customize relationships
// Models/Product.php
public function category()
{
    return $this->belongsTo(Category::class);
}

// 3. Add accessors/mutators
public function getPriceFormattedAttribute()
{
    return '$' . number_format($this->price, 2);
}
```

### 4. Organize Modules

Group related modules in subdirectories:

```
modules/
├── Ecommerce/
│   ├── Product/
│   ├── Category/
│   └── Order/
├── Blog/
│   ├── Post/
│   ├── Tag/
│   └── Comment/
└── User/
    ├── User/
    └── Role/
```

### 5. Custom Templates for Projects

Create project-specific templates:

```bash
# Create template
mkdir resources/stubs/my-project

# Copy and customize
cp -r vendor/laravolt/thunderclap/stubs/laravolt/* resources/stubs/my-project/

# Use consistently
php artisan laravolt:clap --template=my-project
```

### 6. Version Control

Commit generated modules to version control:

```bash
git add modules/
git commit -m "feat: add Product module"
```

### 7. Test Generated Code

Always test generated modules:

```bash
# Run tests
php artisan test

# Or specific test
php artisan test modules/Product/Tests/ProductTest.php
```

### 8. Update Documentation

Document custom changes:

```php
/**
 * Product Module
 * 
 * Generated: 2024-01-08
 * Table: products
 * 
 * Customizations:
 * - Added price formatting
 * - Added category relationship
 * - Custom validation for SKU
 */
class Product extends Model
{
    // ...
}
```

## 🔍 Troubleshooting

### Common Issues and Solutions

#### 1. Module Directory Already Exists

**Error:**
```
Folder /path/to/modules/Product already exist, do you want to overwrite it?
```

**Solutions:**
```bash
# Option 1: Use --force flag
php artisan laravolt:clap --table=products --force

# Option 2: Manually delete
rm -rf modules/Product

# Option 3: Rename existing
mv modules/Product modules/Product.backup
```

#### 2. Table Not Found

**Error:**
```
Table 'database.products' doesn't exist
```

**Solutions:**
```bash
# Check if migrations are run
php artisan migrate

# Check database connection
php artisan db:show

# List available tables
php artisan laravolt:clap
```

#### 3. Doctrine DBAL Issues

**Error:**
```
Class 'Doctrine\DBAL\DriverManager' not found
```

**Solution:**
```bash
composer require doctrine/dbal
```

#### 4. Model Not Detected

**Issue:** Existing model not detected by Thunderclap

**Check:**
```bash
# Verify model location
ls -la app/Models/

# Check class exists
php artisan tinker
>>> class_exists('App\Models\User')

# Check model table
>>> (new App\Models\User)->getTable()
```

**Solutions:**
- Ensure model is in `app/Models/`
- Check namespace is `App\Models\`
- Verify model extends `Illuminate\Database\Eloquent\Model`
- Check table name matches

#### 5. Enhancement Fails

**Error:**
```
✗ Error enhancing model: Syntax error in file
```

**Recovery:**
```bash
# Backup is automatically restored
# Check backup files
ls -la app/Models/*.backup.*

# Manual restore if needed
cp app/Models/User.php.backup.20240108173221 app/Models/User.php
```

#### 6. Generated Code Has Syntax Errors

**Issue:** Generated files have syntax errors

**Solutions:**
```bash
# Run code formatter
vendor/bin/pint modules/Product/

# Check generated files
php artisan tinker
>>> require 'modules/Product/Models/Product.php'

# Validate syntax
find modules/Product -name "*.php" -exec php -l {} \;
```

#### 7. Routes Not Working

**Issue:** Generated routes return 404

**Check:**
```bash
# List routes
php artisan route:list --path=modules

# Check service provider registered
php artisan config:clear
php artisan route:clear
```

**Solutions:**

Register module service provider in `config/app.php`:
```php
'providers' => [
    // ...
    Modules\Product\ProductServiceProvider::class,
],
```

Or use auto-discovery in `composer.json`:
```json
{
    "extra": {
        "laravel": {
            "providers": [
                "Modules\\Product\\ProductServiceProvider"
            ]
        }
    }
}
```

#### 8. Views Not Found

**Error:**
```
View [product::index] not found
```

**Solutions:**
```bash
# Clear view cache
php artisan view:clear

# Check view path registered in ServiceProvider
# Verify resources/views/ directory exists

# Register views manually in ServiceProvider
public function boot()
{
    $this->loadViewsFrom(__DIR__.'/resources/views', 'product');
}
```

#### 9. Livewire Component Not Found

**Error:**
```
Livewire component [modules.product.product-table-view] not found
```

**Solutions:**
```bash
# Clear Livewire cache
php artisan livewire:discover

# Check ServiceProvider registers component
public function boot()
{
    Livewire::component(
        'modules.product.product-table-view', 
        ProductTableView::class
    );
}
```

#### 10. Foreign Key Constraint Errors

**Issue:** Deleting records fails with foreign key constraint

**Solution:**

Add cascading to migrations:
```php
$table->foreignId('category_id')
    ->constrained()
    ->onDelete('cascade');
```

Or handle in model:
```php
public static function boot()
{
    parent::boot();
    
    static::deleting(function($product) {
        // Handle related records
    });
}
```

### Debug Mode

Enable debug mode for troubleshooting:

```php
// In ServiceProvider or Controller
\DB::enableQueryLog();

// Run code

// View queries
dd(\DB::getQueryLog());
```

### Getting Help

If issues persist:

1. **Check Laravel Logs**: `storage/logs/laravel.log`
2. **Enable Debug Mode**: Set `APP_DEBUG=true` in `.env`
3. **Check Permissions**: Ensure `modules/` is writable
4. **Clear Caches**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

## 🎯 Real-World Examples

### Example 1: E-commerce Product Management

**Database Schema:**
```sql
CREATE TABLE products (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    category_id BIGINT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    sku VARCHAR(100) UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

**Generate Module:**
```bash
php artisan laravolt:clap --table=products
```

**Customize After Generation:**

```php
// Models/Product.php - Add relationships
public function category()
{
    return $this->belongsTo(Category::class);
}

public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

// Add scopes
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

public function scopeInStock($query)
{
    return $query->where('stock', '>', 0);
}

// Requests/Store.php - Enhanced validation
public function rules(): array
{
    return [
        'category_id' => ['required', 'exists:categories,id'],
        'name' => ['required', 'string', 'max:255'],
        'slug' => ['required', 'string', 'unique:products,slug'],
        'description' => ['required', 'string', 'min:10'],
        'price' => ['required', 'numeric', 'min:0.01'],
        'stock' => ['required', 'integer', 'min:0'],
        'sku' => ['required', 'string', 'unique:products,sku'],
        'is_active' => ['boolean'],
    ];
}

// Controllers/ProductController.php - Add custom actions
public function duplicate(Product $product)
{
    $newProduct = $product->replicate();
    $newProduct->name = $product->name . ' (Copy)';
    $newProduct->sku = $product->sku . '-COPY';
    $newProduct->save();
    
    return redirect()
        ->route('modules::product.edit', $newProduct)
        ->withSuccess('Product duplicated successfully');
}
```

### Example 2: Blog Post Management

**Database Schema:**
```sql
CREATE TABLE posts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    author_id BIGINT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    content TEXT NOT NULL,
    excerpt VARCHAR(500),
    featured_image VARCHAR(255),
    status VARCHAR(20) DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);
```

**Generate Module:**
```bash
php artisan laravolt:clap --table=posts --module=BlogPost
```

**Customizations:**

```php
// Models/BlogPost.php
protected $casts = [
    'published_at' => 'datetime',
];

public function author()
{
    return $this->belongsTo(User::class, 'author_id');
}

public function scopePublished($query)
{
    return $query->where('status', 'published')
        ->whereNotNull('published_at')
        ->where('published_at', '<=', now());
}

public function scopeDraft($query)
{
    return $query->where('status', 'draft');
}

// TableView
public function data(): Builder
{
    return BlogPost::query()
        ->with('author')
        ->autoSort($this->sortPayload())
        ->autoSearch(trim($this->search))
        ->latest('published_at');
}

public function columns(): array
{
    return [
        Numbering::make('No'),
        Text::make('title')->sortable(),
        Text::make('author.name', 'Author'),
        Text::make('status')->sortable(),
        Date::make('published_at')->sortable(),
        RestfulButton::make('modules::blog-post'),
    ];
}
```

### Example 3: User Management with Roles

**Using Existing User Model:**
```bash
# Check existing model
php artisan laravolt:models --table=users

# Enhance and generate
php artisan laravolt:clap --table=users --use-existing-models
```

**Result:**
- ✅ Existing `App\Models\User` enhanced
- ✅ Admin CRUD generated in `modules/User/`
- ✅ Searchable by name, email

## 📚 API Reference

### LaravoltTransformer Methods

Public methods for customizing code generation:

```php
// Set columns for generation
setColumns(Collection $columns): void

// Generate searchable columns array
toSearchableColumns(): string

// Generate validation rules
toValidationRules(): string

// Generate language fields
toLangFields(): string

// Generate table headers (HTML)
toTableHeaders(): string

// Generate table fields (HTML)
toTableFields(): string

// Generate detail fields (HTML)
toDetailFields(string $objectName): string

// Generate form create fields
toFormCreateFields(): string

// Generate form edit fields
toFormEditFields(): string

// Generate TableView fields
toTableViewFields(): string

// Generate factory attributes
toTestFactoryAttributes(): string

// Generate update test attributes
toTestUpdateAttributes(): string
```

### ModelDetector Methods

Methods for detecting and analyzing models:

```php
// Detect existing model for table
detectExistingModel(string $table): ?array

// Get all models in app/Models
getAllModels(): array

// Check if model needs enhancement
needsEnhancement(string $modelClass): array

// Get table name from model
getTableFromModel(string $modelClass): ?string

// Suggest model name for table
suggestModelName(string $table): string
```

### ModelEnhancer Methods

Methods for enhancing existing models:

```php
// Enhance model with traits and properties
enhanceModel(array $modelInfo, array $enhancement, array $searchableColumns = []): bool

// Create backup of model file
createBackup(string $modelPath): string

// Remove backup of model file
removeBackup(string $backupPath): bool

// Restore model from backup
restoreFromBackup(string $modelPath, string $backupPath): bool
```

## 🤝 Contributing

We welcome contributions! To contribute to Thunderclap:

1. **Fork the Repository**
2. **Create Feature Branch**: `git checkout -b feature/my-feature`
3. **Make Changes**: Follow PSR-12 coding standards
4. **Add Tests**: Ensure new functionality is tested
5. **Commit**: `git commit -m "feat: add my feature"`
6. **Push**: `git push origin feature/my-feature`
7. **Create Pull Request**

### Development Setup

```bash
git clone https://github.com/laravolt/laravolt
cd packages/thunderclap
composer install
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/ThunderclapTest.php

# With coverage
php artisan test --coverage
```

## 📝 Changelog

See [CHANGELOG.md](../../CHANGELOG.md) for recent changes.

## 📄 License

The Laravolt Thunderclap package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Credits

Thunderclap is part of the [Laravolt](https://laravolt.dev) ecosystem.

Created by [Laravolt Team](https://laravolt.dev).

## 🔗 Related Packages

- **[Laravolt Suitable](../suitable)** - Provides AutoFilter, AutoSearch, AutoSort traits
- **[Laravolt Preline Form](../preline-form)** - Form builder used in generated views
- **[Laravolt Platform](../../src/Platform)** - Core platform features

## 📖 Additional Resources

- **Documentation**: [https://laravolt.dev/docs/thunderclap](https://laravolt.dev/docs/thunderclap)
- **Forum**: [https://laravolt.dev/forum](https://laravolt.dev/forum)
- **Issues**: [https://github.com/laravolt/laravolt/issues](https://github.com/laravolt/laravolt/issues)

---

Made with ⚡ by [Laravolt](https://laravolt.dev)
