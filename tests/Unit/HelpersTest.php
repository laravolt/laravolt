<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit;

use Laravolt\Tests\UnitTest;
use function Laravolt\number_to_terbilang;
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

    /**
     * @dataProvider provideTerbilang
     */
    public function test_number_to_terbilang($number, $terbilang)
    {
        $this->assertEquals($terbilang, number_to_terbilang($number));
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

    public function provideTerbilang()
    {
        return [
            [1, 'satu rupiah'],
            [10, 'sepuluh rupiah'],
            [100, 'seratus rupiah'],
            [1000, 'seribu rupiah'],
            [10_000, 'sepuluh ribu rupiah'],
            [100_000, 'seratus ribu rupiah'],
            [1_000_000, 'satu juta rupiah'],
            [1_000_000_000, 'satu milyar rupiah'],
            [1_000_000_000_000, 'satu triliun rupiah'],
            [1_000_000_000_000_000, 'satu kuadriliun rupiah'],
            [1_000_000_000_000_000_000, 'seribu kuadriliun rupiah'],
            [1_234, 'seribu dua ratus tiga puluh empat rupiah'],
            [1_234_567, 'satu juta dua ratus tiga puluh empat ribu lima ratus enam puluh tujuh rupiah'],
            [25_100_671.00, 'dua puluh lima juta seratus ribu enam ratus tujuh puluh satu rupiah'],
            [25_100_671.123, 'dua puluh lima juta seratus ribu enam ratus tujuh puluh satu rupiah koma satu dua tiga'],
        ];
    }
}
