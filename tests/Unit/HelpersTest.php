<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit;

use Laravolt\Tests\UnitTest;

use function Laravolt\number_to_rupiah;
use function Laravolt\number_to_terbilang;
use function Laravolt\platform_path;

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
     * @dataProvider provideTerbilangWithSuffix
     */
    public function test_number_to_terbilang($number, $terbilang)
    {
        $this->assertEquals($terbilang, number_to_terbilang($number));
    }

    /**
     * @dataProvider provideTerbilangWithoutSuffix
     */
    public function test_number_to_terbilang_without_suffix($number, $terbilang)
    {
        $this->assertEquals($terbilang, number_to_terbilang($number, ''));
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

    public function provideTerbilangWithSuffix()
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
            [1_000_000_000_000_000, 'seribu triliun rupiah'],
            [1_234, 'seribu dua ratus tiga puluh empat rupiah'],
            [1_234_567, 'satu juta dua ratus tiga puluh empat ribu lima ratus enam puluh tujuh rupiah'],
            [25_100_671.00, 'dua puluh lima juta seratus ribu enam ratus tujuh puluh satu rupiah'],
            [25_100_671.123, 'dua puluh lima juta seratus ribu enam ratus tujuh puluh satu rupiah koma satu dua tiga'],
            [99_841_500, 'sembilan puluh sembilan juta delapan ratus empat puluh satu ribu lima ratus rupiah'],
            [101_000, 'seratus satu ribu rupiah'],
            [1_001_000, 'satu juta seribu rupiah'],
            [1_010_000, 'satu juta sepuluh ribu rupiah'],
            [1_010_010, 'satu juta sepuluh ribu sepuluh rupiah'],
        ];
    }

    public function provideTerbilangWithoutSuffix()
    {
        return [
            [1, 'satu'],
            [10, 'sepuluh'],
            [100, 'seratus'],
            [1000, 'seribu'],
            [10_000, 'sepuluh ribu'],
            [100_000, 'seratus ribu'],
            [1_000_000, 'satu juta'],
            [1_000_000_000, 'satu milyar'],
            [1_000_000_000_000, 'satu triliun'],
            [1_000_000_000_000_000, 'seribu triliun'],
            [1_234, 'seribu dua ratus tiga puluh empat'],
            [1_234_567, 'satu juta dua ratus tiga puluh empat ribu lima ratus enam puluh tujuh'],
            [25_100_671.00, 'dua puluh lima juta seratus ribu enam ratus tujuh puluh satu'],
            [25_100_671.123, 'dua puluh lima juta seratus ribu enam ratus tujuh puluh satu koma satu dua tiga'],
            [99_841_500, 'sembilan puluh sembilan juta delapan ratus empat puluh satu ribu lima ratus'],
            [101_000, 'seratus satu ribu'],
            [1_001_000, 'satu juta seribu'],
            [1_010_000, 'satu juta sepuluh ribu'],
            [1_010_010, 'satu juta sepuluh ribu sepuluh'],
        ];
    }
}
