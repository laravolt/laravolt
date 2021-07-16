<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit;

use Laravolt\Tests\UnitTest;
use function Laravolt\platform_path;
use function Laravolt\number_to_rupiah;

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

    /**
     * @dataProvider provideRupiah
     */
    public function test_number_to_rupiah($number, $defaultOutput, $outputWithoutPrefix, $outputWithoutDecimals)
    {
        $this->assertEquals($defaultOutput, number_to_rupiah($number));
        $this->assertEquals($outputWithoutPrefix, number_to_rupiah($number, 2, false));
        $this->assertEquals($outputWithoutDecimals, number_to_rupiah($number, 0, true));
    }

    public function provideRupiah()
    {
        return [
            [100, 'Rp100,00', '100,00', 'Rp100'],
            [1000, 'Rp1.000,00', '1.000,00', 'Rp1.000'],
            [10000, 'Rp10.000,00', '10.000,00', 'Rp10.000'],
            [1234.56, 'Rp1.234,56', '1.234,56', 'Rp1.235'],
            [999_999_999.123456, 'Rp999.999.999,12', '999.999.999,12', 'Rp999.999.999'],
            [999_999_999.666, 'Rp999.999.999,67', '999.999.999,67', 'Rp1.000.000.000'],
            [999_999_999.664, 'Rp999.999.999,66', '999.999.999,66', 'Rp1.000.000.000'],
        ];
    }
}
