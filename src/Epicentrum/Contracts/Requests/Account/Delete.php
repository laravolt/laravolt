<?php

declare(strict_types=1);

namespace Laravolt\Epicentrum\Contracts\Requests\Account;

interface Delete
{
    public function authorize();

    public function rules();
}
