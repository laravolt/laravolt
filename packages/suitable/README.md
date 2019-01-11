# SUI-TABLE
Semantic-UI table builder for Laravel.

## Version Compatibility

 Laravel  | Suitable
:---------|:----------
 5.2.x    | 1.x
 5.3.x    | 2.x
 5.4.x    | 2.x
 5.5.x    | 2.x
 5.6.x    | 2.x
 5.7.x    | 2.x

## Installation

### Install Package
``` bash
composer require laravolt/suitable
```

### Service Provider
_Skip this step for Laravel 5.5 or above._

```php
Laravolt\Suitable\ServiceProvider::class,
```

### Facade
_Skip this step for Laravel 5.5 or above._

```php
'Suitable'  => Laravolt\Suitable\Facade::class,
```

## Usage

### Basic
```php
{!! Suitable::source(User::all())
->id('table1')
->title('Users')
->tableClass('ui table')
->search(false)
->columns([
    new \Laravolt\Suitable\Components\Checkall(),
    ['header' => 'Nama', 'field' => 'name'],
    ['header' => 'Email', 'field' => 'email'],
])
->render() !!}
```

### Columns Definition

#### `field`
```php
{!! Suitable::source(User::all())
->columns([
    ['header' => 'Email', 'field' => 'email'],
    ['header' => 'Bio', 'field' => 'profile.bio'], // nested attribute
])
->render() !!}`
```

#### `view`
```php
{!! Suitable::source(User::all())
->columns([
    ['header' => 'Address', 'view' => 'components.address'],
])
->render() !!}`
```

`views/components/address.blade.php`
```html
<address>
  Address:<br>
  {{ $data->address_1 }}<br>
  {{ $data->address_2 }}<br>
  {{ $data->city }}, {{ $data->state }}
</address>
```

#### `raw`
```php
{!! Suitable::source(User::all())
->columns([
    [
        'header' => 'Roles', 
        'raw' => function($data){
            // do anything here and don't forget to return String
            return $data->roles->implode('name', ', '); // output: "role1, role2, role3"
        }
    ],
])
->render() !!}`
```

#### `ColumnInterface`
```php
{!! Suitable::source(User::all())
->columns([
    new \App\Columns\StatusColumn('Status'),
])
->render() !!}
```
##### Contract
```php
<?php
namespace Laravolt\Suitable\Columns;

interface ColumnInterface
{
    public function header();

    public function headerAttributes();

    public function cell($cell, $collection, $loop);

    public function cellAttributes($cell);
}
```

##### Implementation
`StatusColumn.php`

```php
<?php

namespace App\Columns;

use Laravolt\Suitable\Columns\ColumnInterface;

class StatusColumn implements ColumnInterface
{
    protected $header;

    public function __construct($header)
    {
        $this->header = $header;
    }

    public function header()
    {
        return $this->header;
    }

    public function cell($cell, $collection, $loop)
    {
        return sprintf("<div class='ui label'>%s</div>", $cell->status);
    }

    public function headerAttributes()
    {
        return [];
    }

    public function cellAttributes($cell)
    {
        return [];
    }
}

```

##### Built In Columns
1. 
