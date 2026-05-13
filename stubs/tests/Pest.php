<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        Str::createRandomStringsNormally();
        Str::createUuidsNormally();
        Http::preventStrayRequests();
        Process::preventStrayProcesses();
        Sleep::fake();

        $this->freezeTime();
    })
    ->in('Feature', 'Unit');

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Browser');

pest()->browser()->timeout(10000);

expect()->extend('toBeOne', fn () => $this->toBe(1));

function something(): void
{
    // ..
}
