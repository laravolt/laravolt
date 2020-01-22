<?php

declare(strict_types=1);

namespace Laravolt\Contracts;

interface CanChangePassword
{
    public function setPassword($password, $mustBeChanged = false);

    public function passwordMustBeChanged();

    public function getEmailForNewPassword();
}
