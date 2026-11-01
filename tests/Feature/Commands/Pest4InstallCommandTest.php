<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravolt\platform_path;

// Each test installs into an empty project directory, with Composer faked.
beforeEach(function (): void {
    $this->originalBasePath = $this->app->basePath();
    $this->project = sys_get_temp_dir().'/laravolt-pest4-'.bin2hex(random_bytes(4));

    File::ensureDirectoryExists($this->project.'/vendor/phpunit/phpunit');
    File::put($this->project.'/.gitignore', "/vendor\n");
    $this->app->setBasePath($this->project);
});

afterEach(function (): void {
    $this->app->setBasePath($this->originalBasePath);
    File::deleteDirectory($this->project);
});

it('installs Pest with Composer and writes the test setup', function (): void {
    Process::fake();

    $this->artisan('laravolt:pest4-install')->assertSuccessful();

    Process::assertRan(fn ($process): bool => $process->path === $this->project
        && $process->command === ['composer', 'remove', 'phpunit/phpunit', '--dev']);
    Process::assertRan(fn ($process): bool => $process->command === [
        'composer', 'require', 'pestphp/pest:^4.0', 'pestphp/pest-plugin-browser:^4.0', '--dev', '--with-all-dependencies',
    ]);

    expect(File::get($this->project.'/tests/Pest.php'))->toBe(File::get(platform_path('stubs/tests/Pest.php')))
        ->and(File::get($this->project.'/tests/Browser/LoginTest.php'))->toBe(File::get(platform_path('stubs/tests/Browser/LoginTest.php')))
        ->and(File::get($this->project.'/phpunit.xml'))->toContain('<testsuite name="Feature">')
        ->and($this->project.'/PEST_MIGRATION_GUIDE.md')->toBeFile()
        ->and(File::get($this->project.'/.gitignore'))->toContain("/playwright-report\n");
});

it('does nothing when Pest is already installed', function (): void {
    File::ensureDirectoryExists($this->project.'/vendor/pestphp/pest');
    Process::fake();

    $this->artisan('laravolt:pest4-install')
        ->expectsOutputToContain('Pest is already installed')
        ->assertSuccessful();

    Process::assertNothingRan();
    expect($this->project.'/tests/Pest.php')->not->toBeFile();
});

it('reinstalls with --force', function (): void {
    File::ensureDirectoryExists($this->project.'/vendor/pestphp/pest');
    Process::fake();

    $this->artisan('laravolt:pest4-install', ['--force' => true])->assertSuccessful();

    Process::assertRan(fn ($process): bool => ($process->command[1] ?? null) === 'require');
});

it('keeps existing files and adds each .gitignore entry once', function (): void {
    File::ensureDirectoryExists($this->project.'/tests');
    File::put($this->project.'/tests/Pest.php', "<?php // mine\n");
    Process::fake();

    $this->artisan('laravolt:pest4-install')->assertSuccessful();
    $this->artisan('laravolt:pest4-install', ['--force' => true])->assertSuccessful();

    $gitignore = File::get($this->project.'/.gitignore');

    expect(File::get($this->project.'/tests/Pest.php'))->toBe("<?php // mine\n")
        ->and(substr_count($gitignore, "/test-results\n"))->toBe(1)
        ->and(substr_count($gitignore, "/build/coverage\n"))->toBe(1);
});

it('stops when Composer cannot install Pest', function (): void {
    Process::fake(['*' => Process::result(exitCode: 1)]);

    $this->artisan('laravolt:pest4-install')
        ->expectsOutputToContain('Failed to install Pest v4')
        ->assertFailed();

    expect($this->project.'/tests/Pest.php')->not->toBeFile();
});
