<?php

namespace Laravolt\Epicentrum\Contracts\Requests\Account;

interface Update
{
    public function authorize();

    public function rules();
}
