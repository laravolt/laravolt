<?php

declare(strict_types=1);

use App\Http\Middleware\Authenticate;

arch()->preset()->php();
arch()->preset()->strict()->ignoring(Authenticate::class);
arch()->preset()->security();

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

//
