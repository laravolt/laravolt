<?php

declare(strict_types=1);

namespace Laravolt\Platform\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class Pest4InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:pest4-install {--force : Force installation even if Pest is already installed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and configure Pest v4 for testing, migrating from PHPUnit';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('🧪 Installing Pest v4...');
        $this->newLine();

        // Check if Pest is already installed
        if ($this->isPestInstalled() && !$this->option('force')) {
            $this->info('✅ Pest is already installed. Use --force to reinstall.');
            return self::SUCCESS;
        }

        // Remove PHPUnit if it exists
        if ($this->isPhpUnitInstalled()) {
            $this->info('🗑️  Removing PHPUnit...');
            $this->runComposerCommand(['remove', 'phpunit/phpunit', '--dev']);
        }

        // Install Pest v4
        $this->info('📦 Installing Pest v4 with all dependencies...');
        $success = $this->runComposerCommand([
            'require', 
            'pestphp/pest', 
            '--dev', 
            '--with-all-dependencies'
        ]);

        if (!$success) {
            $this->error('❌ Failed to install Pest v4');
            return self::FAILURE;
        }

        // Create Pest configuration
        $this->info('⚙️  Creating Pest configuration...');
        $this->createPestConfiguration();

        // Update .gitignore
        $this->info('📝 Updating .gitignore...');
        $this->updateGitignore();

        // Create migration guide
        $this->info('📚 Creating migration guide...');
        $this->createMigrationGuide();

        $this->newLine();
        $this->info('✅ Pest v4 installation complete!');
        $this->info('🚀 Run "./vendor/bin/pest" to execute your tests');
        $this->info('📖 Check PEST_MIGRATION_GUIDE.md for migration instructions');
        
        return self::SUCCESS;
    }

    /**
     * Check if Pest is already installed
     */
    private function isPestInstalled(): bool
    {
        return File::exists(base_path('vendor/pestphp/pest'));
    }

    /**
     * Check if PHPUnit is installed
     */
    private function isPhpUnitInstalled(): bool
    {
        return File::exists(base_path('vendor/phpunit/phpunit'));
    }

    /**
     * Run a composer command
     */
    private function runComposerCommand(array $command): bool
    {
        $process = new Process(array_merge(['composer'], $command), base_path());
        $process->setTimeout(300); // 5 minutes timeout
        
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        return $process->isSuccessful();
    }

    /**
     * Create Pest configuration files
     */
    private function createPestConfiguration(): void
    {
        // Create tests/Pest.php
        $pestConfigPath = base_path('tests/Pest.php');
        if (!File::exists($pestConfigPath)) {
            $pestConfig = <<<'PHP'
<?php

declare(strict_types=1);

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
PHP;

            File::put($pestConfigPath, $pestConfig);
            $this->line("   ✅ Created tests/Pest.php");
        }

        // Create pest.xml (replacing phpunit.xml for Pest)
        $pestXmlPath = base_path('pest.xml');
        if (!File::exists($pestXmlPath)) {
            $pestXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/pestphp/pest/resources/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="APP_MAINTENANCE_DRIVER" value="file"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="PULSE_ENABLED" value="false"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>
XML;

            File::put($pestXmlPath, $pestXml);
            $this->line("   ✅ Created pest.xml");
        }
    }

    /**
     * Update .gitignore with Pest-specific entries
     */
    private function updateGitignore(): void
    {
        $gitignorePath = base_path('.gitignore');
        if (!File::exists($gitignorePath)) {
            return;
        }

        $entries = [
            '/build/coverage',
            '/pestphp-coverage-result.xml',
            '/pestphp-execution-result.xml',
        ];

        $contents = File::get($gitignorePath);
        $lines = explode("\n", $contents);
        
        foreach ($entries as $entry) {
            if (!in_array($entry, $lines, true)) {
                File::append($gitignorePath, $entry . "\n");
                $this->line("   ✅ Added {$entry} to .gitignore");
            }
        }
    }

    /**
     * Create migration guide
     */
    private function createMigrationGuide(): void
    {
        $guidePath = base_path('PEST_MIGRATION_GUIDE.md');
        
        $guide = <<<'MARKDOWN'
# PHPUnit to Pest v4 Migration Guide

## Overview

This guide helps you migrate your existing PHPUnit tests to Pest v4.

## Installation Complete

✅ Pest v4 has been installed with all dependencies
✅ Configuration files have been created
✅ .gitignore has been updated

## Manual Migration Steps

### 1. Convert Test Classes to Functions

**Before (PHPUnit):**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_example(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
```

**After (Pest v4):**
```php
<?php

test('example', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});
```

### 2. Convert Traits Usage

**Before:**
```php
class ExampleTest extends TestCase
{
    use RefreshDatabase;
}
```

**After:**
```php
uses(RefreshDatabase::class);
```

### 3. Convert Setup Methods

**Before:**
```php
protected function setUp(): void
{
    parent::setUp();
    // setup code
}
```

**After:**
```php
beforeEach(function () {
    // setup code
});
```

### 4. Update Assertions (Optional)

You can optionally use Pest's expectation API:

**Before:**
```php
$this->assertTrue($value);
$this->assertEquals('expected', $actual);
```

**After (Optional):**
```php
expect($value)->toBeTrue();
expect($actual)->toBe('expected');
```

## Running Tests

```bash
# Run all tests
./vendor/bin/pest

# Run specific test suite
./vendor/bin/pest --testsuite=Feature

# Run with coverage
./vendor/bin/pest --coverage
```

## Resources

- [Pest Documentation](https://pestphp.com/docs)
- [Migration Guide](https://pestphp.com/docs/migrating-from-phpunit)
- [Expectations API](https://pestphp.com/docs/expectations)

## Need Help?

Run `./vendor/bin/pest --help` for available options and commands.
MARKDOWN;

        File::put($guidePath, $guide);
        $this->line("   ✅ Created PEST_MIGRATION_GUIDE.md");
    }
}