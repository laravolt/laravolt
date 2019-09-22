<?php

namespace Laravolt\Auth\Contracts;

interface UserRegistrar
{
    public function validate(array $data);

    public function register(array $data);
}
