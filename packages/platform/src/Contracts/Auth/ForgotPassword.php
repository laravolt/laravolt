<?php

namespace Laravolt\Auth\Contracts;

interface ForgotPassword
{
    public function rules();

    public function getUserByIdentifier($identifier);
}
