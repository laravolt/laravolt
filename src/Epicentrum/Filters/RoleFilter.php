<?php

declare(strict_types=1);

namespace Laravolt\Epicentrum\Filters;

use Laravolt\Platform\Models\Role;
use Laravolt\Ui\Filters\DropdownFilter;

class RoleFilter extends DropdownFilter
{
    protected string $label = 'Roles';

    public function apply($data, $value)
    {
        if ($value) {
            $data->whereHas('roles', fn ($query) => $query->where('id', $value));
        }

        return $data;
    }

    public function options(): array
    {
        return Role::query()->pluck('name', 'id')->prepend('All Roles', '0')->toArray();
    }
}
