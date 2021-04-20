<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit;

use Laravolt\Tests\UnitTest;

class HelpersTest extends UnitTest
{
    public function testPlatformPath()
    {
        $actual = platform_path('resources/files/file.txt');
        $expected = realpath(__DIR__.'/../../resources/files/file.txt');

        $this->assertSame($expected, $actual);
        $this->assertFileExists($actual);
    }

    public function testPlatformPathWithTrailingSlash()
    {
        $actual = platform_path('/resources/files/file.txt');
        $expected = realpath(__DIR__.'/../../resources/files/file.txt');

        $this->assertSame($expected, $actual);
        $this->assertFileExists($actual);
    }
}
