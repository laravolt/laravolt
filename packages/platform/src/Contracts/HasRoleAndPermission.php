<?php

declare(strict_types=1);

namespace Laravolt\Contracts;

interface HasRoleAndPermission
{
    public function roles();

    public function hasRole($role, $checkAll = false);

    public function assignRole($role);

    public function revokeRole($role);

    public function hasPermission($permission, $checkAll = false);
}
