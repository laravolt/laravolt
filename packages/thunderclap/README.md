# Thunderclap
Laravel CRUD generator, especially for Laravolt platform.

## Installation

Install thunderclap via composer:

```bash
composer require laravolt/thunderclap
```
Add service provider (only for Laravel <= 5.4):

```php
Laravolt\Thunderclap\ServiceProvider::class,
```

**WARNING!!**
Thunderclap assume followings package already installed in your application:

```json
{
	"nwidart/laravel-modules": "^5.0",
	"sofa/eloquence": "^5.6"
}
```

## Configuration

publish configuration file `php artisan vendor:publish --provider='Laravolt\Thunderclap\ServiceProvider' --tag=config` there will be 
file `config/laravolt/thunderclap.php` and example code inside it.

```php
<?php

return [
    // specify columns that you want to except
    'columns' => [
        'except' => ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token']
    ],
    'view' => [
        'extends' => 'layout'
    ],
    // custom your routes specification
    'routes'     => [
        'prefix'    => '',
        'middleware' => [],
    ],
    // custom your namespace per module
    'namespace'  => 'Modules',
    'target_dir' => base_path('modules'),
    
    // Template skeleton (stubs)
    'default'    => 'laravolt',

    // name => directory path, relative with stubs directory or absolute path
    'templates'  => [
        'laravolt' => 'laravolt',
    ],    
];
```

## Usage
1. Run the command and choose your table
```bash
php artisan laravolt:clap
```
:clap: clap your hand twice, and follow the magic...
1. Register your module into `composer.json` and `config/app.php`, i.e your table name is `category`
	- composer.json
		```
		"autoload": {
			"psr-4": {
				"App\\": "app/",
				"Modules\\": "modules" <= insert here
			},
			....
		},
		....
		```
	- config/app.php
		```
		'providers' => [
			....
			 /*
            * Package Service Providers...
            */
            
            Modules\Category\Providers\ServiceProvider::class,
                
           /*
            * Application Service Providers...
            */
           ....
		]

		```
1. Run `composer dumpautoload`

## Roadmap

- [x] Database based generator
- [ ] JSON file based generator
- [ ] Relationship
- Field Type
	- [x] Text
	- [x] Textarea
	- [ ] Select
	- [ ] Checkbox
	- [x] Date
	- [x] Datetime
- Table
	- [ ] Multiple delete
	- [ ] Confirm on delete
- Form
	- [ ] Inline error message
	- [ ] Mark required field
- [x] Custom template
- [ ] API generator
- [ ] API documentation
