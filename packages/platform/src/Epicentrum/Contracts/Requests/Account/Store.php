<?php

namespace Laravolt\Epicentrum\Contracts\Requests\Account;

interface Store
{
    public function authorize();

    public function rules();
}
