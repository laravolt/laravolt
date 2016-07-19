# SUI-TABLE
Semantic-UI table builder for Laravel.

> **Please note**, this package is still in early development phase and not recommended to be used in production. Things will changes, things will breaks, until the release of version 1.

## Installation

### Install Package

``` bash
$ composer require laravolt/suitable
```

### Service Provider

    Laravolt\Suitable\ServiceProvider::class,

### Facade

    'Suitable'  => Laravolt\Suitable\Facade::class,

## Usage

``` php
{!! Suitable::source(User::all())
->id('table1')
->title('Users')
->columns([
    new \Laravolt\Suitable\Components\Checkall(),
    ['header' => 'Nama', 'field' => 'name'],
    ['header' => 'Email', 'field' => 'email'],
])
->render() !!}
```
