<?php

require 'vendor/autoload.php';

use Illuminate\Database\Eloquent\Collection;

// Mock the trait and setup
class User {
    public $roles;
    public $permissionsCol;

    public function __construct() {
        $this->roles = new Collection();
        for ($i = 0; $i < 100; $i++) {
            $this->roles->push((object)['id' => $i, 'name' => "role_$i", 'is' => function($other) use ($i) { return $other->id === $i; }]);
        }

        $this->permissionsCol = new Collection();
        for ($i = 0; $i < 1000; $i++) {
            $this->permissionsCol->push((object)['id' => $i, 'name' => "perm_$i", 'getKey' => function() use ($i) { return $i; }]);
        }
    }

    public function permissions() {
        return $this->permissionsCol;
    }

    public function hasRoleOld($role, $checkAll = false): bool {
        if (is_array($role)) {
            $match = 0;
            foreach ($role as $r) {
                $match += (int) $this->hasRoleOld($r, $checkAll);
            }
            if ($checkAll) {
                return $match === count($role);
            }
            return $match > 0;
        }
        if (is_int($role)) {
            $role = $this->roles->firstWhere('id', $role);
        }
        if (!is_object($role)) {
            return false;
        }
        foreach ($this->roles as $assignedRole) {
            if ($role->id === $assignedRole->id) {
                return true;
            }
        }
        return false;
    }

    public function hasRoleNew($role, $checkAll = false): bool {
        if (is_array($role)) {
            foreach ($role as $r) {
                $has = $this->hasRoleNew($r, $checkAll);
                if ($checkAll && !$has) return false;
                if (!$checkAll && $has) return true;
            }
            return $checkAll;
        }
        if (is_int($role)) {
            return $this->roles->contains('id', $role);
        }
        return false;
    }

    public function hasPermissionOld($permission, $checkAll = false) {
        if (is_array($permission)) {
            $match = 0;
            foreach ($permission as $perm) {
                $match += (int) $this->hasPermissionOld($perm);
            }
            if ($checkAll) {
                return $match === count($permission);
            }
            return $match > 0;
        }
        if (is_string($permission)) {
            return (bool) $this->permissions()->where('name', $permission)->first();
        }
        return false;
    }

    public function hasPermissionNew($permission, $checkAll = false) {
        if (is_array($permission)) {
            foreach ($permission as $perm) {
                $has = $this->hasPermissionNew($perm);
                if ($checkAll && !$has) return false;
                if (!$checkAll && $has) return true;
            }
            return $checkAll;
        }
        if (is_string($permission)) {
            return $this->permissions()->contains('name', $permission);
        }
        return false;
    }
}

$user = new User();

// Benchmark array roles (early return vs full evaluation)
$roles = ['missing_1', 'missing_2', 10, 'missing_3', 'missing_4', 50]; // 10 is found at index 2
$start = microtime(true);
for ($i=0; $i<10000; $i++) {
    $user->hasRoleOld($roles);
}
echo "hasRole Old: " . (microtime(true) - $start) . "s\n";

$start = microtime(true);
for ($i=0; $i<10000; $i++) {
    $user->hasRoleNew($roles);
}
echo "hasRole New: " . (microtime(true) - $start) . "s\n";


// Benchmark permissions (where()->first() vs contains())
$permissions = ['missing_1', 'missing_2', 'perm_10', 'missing_3', 'perm_500'];
$start = microtime(true);
for ($i=0; $i<10000; $i++) {
    $user->hasPermissionOld($permissions);
}
echo "hasPermission Old: " . (microtime(true) - $start) . "s\n";

$start = microtime(true);
for ($i=0; $i<10000; $i++) {
    $user->hasPermissionNew($permissions);
}
echo "hasPermission New: " . (microtime(true) - $start) . "s\n";
