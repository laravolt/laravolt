<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Tests\PackageTest;
use Laravolt\Tests\UnitTest;

/*
|--------------------------------------------------------------------------
| Pest Configuration
|--------------------------------------------------------------------------
|
| Pest-style tests (test()/it() closures) need a base TestCase so that the
| Laravel application from Orchestra Testbench is booted before each test.
| Without this binding, helpers such as config() fail with
| "Target class [config] does not exist.".
|
*/

uses(UnitTest::class)->in('Unit/Media');

uses(PackageTest::class, RefreshDatabase::class)->in('Feature/Media');

uses(PackageTest::class)->in('Feature/Commands');

uses(PackageTest::class, RefreshDatabase::class)->in('Feature/Platform');
