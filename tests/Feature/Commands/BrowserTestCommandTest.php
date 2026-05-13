<?php

declare(strict_types=1);

use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Application;
use Illuminate\Process\Factory;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Process;
use Laravolt\Platform\Commands\BrowserTestCommand;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function (): void {
    $app = new Application(getcwd());
    $app->bind(OutputStyle::class, fn ($app, array $parameters) => new OutputStyle(
        $parameters['input'],
        $parameters['output'],
    ));
    $app->instance(ComponentsFactory::class, new ComponentsFactory(new NullOutput));
    $app->instance(ConsoleKernel::class, new class
    {
        public function bootstrap(): void {}

        public function handle($input, $output = null): int
        {
            return 0;
        }

        public function call($command, array $parameters = [], $outputBuffer = null): int
        {
            return 0;
        }

        public function queue($command, array $parameters = []): void {}

        public function all(): array
        {
            return [];
        }

        public function output(): string
        {
            return '';
        }

        public function terminate($input, $status): void {}
    });

    Facade::setFacadeApplication($app);
    Facade::clearResolvedInstance('process');
    Process::swap(new Factory);
});

function browserTestCommandTester(): CommandTester
{
    $command = new BrowserTestCommand;
    $command->setLaravel(Application::getInstance());

    return new CommandTester($command);
}

it('runs browser tests through Pest', function (): void {
    Process::fake();

    expect(browserTestCommandTester()->execute([]))->toBe(0);

    Process::assertRan(function ($process): bool {
        return $process->path === getcwd()
            && $process->timeout === null
            && $process->command === [getcwd().'/vendor/bin/pest', 'tests/Browser', '--no-coverage', '--browser', 'chrome'];
    });
});

it('passes browser test options to Pest', function (): void {
    Process::fake();

    expect(browserTestCommandTester()->execute([
        'path' => 'tests/Browser/LoginTest.php',
        '--debug' => true,
        '--browser' => 'firefox',
        '--parallel' => true,
    ]))->toBe(0);

    Process::assertRan(function ($process): bool {
        return $process->command === [
            getcwd().'/vendor/bin/pest',
            'tests/Browser/LoginTest.php',
            '--no-coverage',
            '--debug',
            '--headed',
            '--browser',
            'firefox',
            '--parallel',
        ];
    });
});

it('returns a failure exit code when Pest fails', function (): void {
    Process::fake([
        '*' => Process::result(exitCode: 1),
    ]);

    expect(browserTestCommandTester()->execute([]))->toBe(1);
});
