<?php

namespace Laravolt\Platform\Contracts;

interface HasRoleAndPermission
{
    public function roles();

    public function hasRole($role, $checkAll = false);

    public function assignRole($role);

    public function revokeRole($role);

    public function hasPermission($permission, $checkAll = false);
}
