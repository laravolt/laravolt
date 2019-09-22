<?php

namespace Laravolt\Auth\Contracts;

use Illuminate\Http\Request;

interface Login
{
    public function rules(Request $request);

    public function credentials(Request $request);
}
