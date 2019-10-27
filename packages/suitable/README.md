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
 5.8.x    | 3.x

## Installation

### Install Package
``` bash
composer require laravolt/suitable
```

### Service Provider
_Skip this step for Laravel >= 5.5._

```php
Laravolt\Suitable\ServiceProvider::class,
```

### Facade
_Skip this step for Laravel >= 5.5._

```php
'Suitable'  => Laravolt\Suitable\Facade::class,
```

## Usage

### Basic
```php
{!! Suitable::source(User::all())
->columns([
    \Laravolt\Suitable\Columns\Numbering::make('No'),
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

    public function sortable();
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

## Advance Usage

### Auto Detect
```php
<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Routing\Controller;
use Laravolt\Suitable\Plugins\Pdf;
use Laravolt\Suitable\Plugins\Spreadsheet;
use Laravolt\Suitable\Tables\BasicTable;

class SuitableController extends Controller
{
    public function __invoke()
    {
        $table = (new BasicTable(new User()));

        return $table->view('etalase::example.suitable');
    }
}
```

### Custom TableView

#### TableView Definition

```php
<?php

namespace App\Table;

use Laravolt\Suitable\Columns\Date;
use Laravolt\Suitable\Columns\DateTime;
use Laravolt\Suitable\Columns\Id;
use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Suitable\TableView;

class UserTable extends TableView
{
    protected function columns()
    {
        return [
            Numbering::make('No'),
            Id::make('id'),
            Text::make('name'),
            Text::make('email'),
            Date::make('created_at'),
            DateTime::make('updated_at'),
        ];
    }
}
```

```php
<?php

namespace Laravolt\Etalase\Http\Controllers;

use App\User;
use Illuminate\Routing\Controller;
use App\Table\UserTable;
use Laravolt\Suitable\Plugins\Pdf;
use Laravolt\Suitable\Plugins\Spreadsheet;

class SuitableController extends Controller
{
    public function __invoke()
    {
        $users = User::autoSort()->paginate(5);
        $userTable = new UserTable($users);

        $table = $userTable
            ->plugins([
                (new Pdf('users.pdf')),
                (new Spreadsheet('users.xls')),
            ]);

        return $table->view('etalase::example.suitable');
    }
}
```

#### Built In Columns
1. `Laravolt\Suitable\Columns\Avatar`
1. `Laravolt\Suitable\Columns\Boolean`
1. `Laravolt\Suitable\Columns\Checkall`
1. `Laravolt\Suitable\Columns\Date`
1. `Laravolt\Suitable\Columns\DateTime`
1. `Laravolt\Suitable\Columns\Id`
1. `Laravolt\Suitable\Columns\Image`
1. `Laravolt\Suitable\Columns\Numbering`
1. `Laravolt\Suitable\Columns\Raw`
1. `Laravolt\Suitable\Columns\RestfulButton`
1. `Laravolt\Suitable\Columns\Text`
1. `Laravolt\Suitable\Columns\View`

## Roadmap
- Rename `TableView` to `Table`
- Rename Toolbars to `Segment\Item`
- Rename DropdownFilter to `DropdownLink`
