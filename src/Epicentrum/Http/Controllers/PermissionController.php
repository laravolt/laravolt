<?php

declare(strict_types=1);

namespace Laravolt\Epicentrum\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function edit()
    {
        $permissions = config('laravolt.epicentrum.models.permission')::all()->sortBy(function ($item) {
            return mb_strtolower($item->name);
        });

        return view('laravolt::permissions.edit', compact('permissions'));
    }

    public function update()
    {
        $descriptions = (array) request('permission', []);
        $permissionModel = config('laravolt.epicentrum.models.permission');

        // One read, then write only the rows whose description changed.
        DB::transaction(function () use ($descriptions, $permissionModel) {
            $permissionModel::query()
                ->whereIn('id', array_keys($descriptions))
                ->get()
                ->each(function ($permission) use ($descriptions) {
                    $permission->description = $descriptions[$permission->getKey()];

                    if ($permission->isDirty('description')) {
                        $permission->save();
                    }
                });
        });

        return redirect()->back()->withSuccess('Permission updated');
    }
}
