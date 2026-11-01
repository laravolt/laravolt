<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Process;

it('runs browser tests through Pest', function (): void {
    Process::fake();

    $this->artisan('laravolt:test:browser')->assertSuccessful();

    Process::assertRan(fn ($process): bool => $process->path === base_path()
        && $process->timeout === null
        && $process->command === [base_path('vendor/bin/pest'), 'tests/Browser', '--no-coverage', '--browser', 'chrome']);
});

it('passes browser test options to Pest', function (): void {
    Process::fake();

    $this->artisan('laravolt:test:browser', [
        'path' => 'tests/Browser/LoginTest.php',
        '--debug' => true,
        '--browser' => 'firefox',
        '--parallel' => true,
    ])->assertSuccessful();

    Process::assertRan(fn ($process): bool => $process->command === [
        base_path('vendor/bin/pest'),
        'tests/Browser/LoginTest.php',
        '--no-coverage',
        '--debug',
        '--headed',
        '--browser',
        'firefox',
        '--parallel',
    ]);
});

it('returns a failure exit code when Pest fails', function (): void {
    Process::fake(['*' => Process::result(exitCode: 1)]);

    $this->artisan('laravolt:test:browser')->assertFailed();
});

it('rejects an unknown browser', function (): void {
    Process::fake();

    $this->artisan('laravolt:test:browser', ['--browser' => 'netscape'])
        ->expectsOutputToContain('Unknown browser [netscape]')
        ->assertFailed();

    Process::assertNothingRan();
});
