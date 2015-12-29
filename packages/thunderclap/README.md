# Thunderclap
Laravel CRUD generator that utilizing the following libraries:

* [l5-repository](https://github.com/andersao/l5-repository) to interact with database.
* [Semantic-UI](http://semantic-ui.com/) to built beautiful interface.

## Installation

Install thunderclap via composer:

```bash
composer require laravolt/thunderclap
```
Add service provider:

```php
Laravolt\Thunderclap\ServiceProvider::class,
```

## Usage

```bash
php artisan clap
```

:clap: clap your hand twice, and follow the magic...


## Roadmap

- [x] Database based generator
- [ ] JSON file based generator
- [ ] Relationship
- Field Type
	- [x] Text
	- [ ] Textarea
	- [ ] Select
	- [ ] Checkbox
	- [ ] Date
	- [ ] Datetime
- Table
	- [ ] Multiple delete
	- [ ] Confirm on delete
- Form
	- [ ] Inline error message
	- [ ] Mark required field
- [ ] Custom template
- [ ] API generator
- [ ] API documentation