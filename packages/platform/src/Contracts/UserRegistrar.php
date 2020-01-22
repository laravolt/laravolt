<?php

namespace Laravolt\Contracts;

interface UserRegistrar
{
    public function validate(array $data);

    public function register(array $data);
}
