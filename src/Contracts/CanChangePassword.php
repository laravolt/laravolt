<?php

declare(strict_types=1);

namespace Laravolt\Contracts;

interface CanChangePassword
{
    public function setPassword($password, $mustBeChanged = false): bool;

    public function passwordMustBeChanged($durationInDays = null): bool;

    public function getEmailForNewPassword(): string;
}
