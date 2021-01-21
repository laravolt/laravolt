<?php

namespace Laravolt\Asset\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class AssetCollectionNotFoundException extends \Exception implements ProvidesSolution
{
    public function getSolution(): Solution
    {
        return BaseSolution::create('Asset collection tidak ditemukan!')
            ->setSolutionDescription(
                sprintf(
                    'Silakan cek kembali file %s dan pastikan nama collection yang Anda maksud sudah terdaftar. '
                    .'Jika file tersebut belum ada, jalankan "php artisan vendor:publish --tag=laravolt-config"',
                    config_path('laravolt/asset.php')
                )
            )->setDocumentationLinks([
                'Pelajari lebih lanjut apa itu asset collection' => 'https://laravolt.dev/docs/asset/',
            ]);
    }
}
