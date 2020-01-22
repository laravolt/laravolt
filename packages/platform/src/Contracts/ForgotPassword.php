<?php

namespace Laravolt\Contracts;

interface ForgotPassword
{
    public function rules();

    public function getUserByIdentifier($identifier);
}
