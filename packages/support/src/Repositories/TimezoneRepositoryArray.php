<?php

namespace Laravolt\Support\Repositories;

use Laravolt\Support\Contracts\TimezoneRepository;

/**
 * Class TimezoneRepositoryEloquent
 * @package namespace App\Repositories;
 */
class TimezoneRepositoryArray implements TimezoneRepository
{
    public function lists()
    {
        return array_flip(config('timezones'));
    }

}
