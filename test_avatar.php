<?php

$app = require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Facade;
use Illuminate\Container\Container;
use Laravolt\Avatar\Avatar;
use Intervention\Image\Typography\FontFactory;

$container = new Container();
Facade::setFacadeApplication($container);

$avatar = new Avatar([], null);
$avatar->create('John Doe')->toBase64();
echo "Avatar creation OK\n";
