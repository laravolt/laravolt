<?php

namespace Laravolt\Epicentrum\Http\Controllers;

use Illuminate\Routing\Controller;

class PermissionController extends Controller
{
    public function edit()
    {
        $permissions = config('laravolt.epicentrum.models.permission')::all()->sortBy(function ($item) {
            return strtolower($item->name);
        });

        return view('laravolt::permissions.edit', compact('permissions'));
    }

    public function update()
    {
        foreach (request('permission', []) as $key => $description) {
            config('laravolt.epicentrum.models.permission')::whereId($key)->update(['description' => $description]);
        }

        return redirect()->back()->withSuccess('Permission updated');
    }
}
