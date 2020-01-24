<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Laravolt\Platform\Models\Role;
use Laravolt\Suitable\Builder;
use Laravolt\Workflow\Models\Module;
use Laravolt\Workflow\TableView\ModuleTableView;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = \Laravolt\Workflow\Models\Module::all()->sort(function ($a, $b) {
            if (!$a->process_definition_key) {
                return 1;
            }

            if (!$b->process_definition_key) {
                return -1;
            }

            return strnatcmp((string) $a->process_definition_key, (string) $b->process_definition_key);
        });

        $table = (new ModuleTableView($modules))->decorate(function (Builder $builder) {
            $builder->row('workflow::module._row');
        });

        return $table->view('workflow::module.index');
    }

    public function edit($id)
    {
        $module = Module::findOrFail($id);
        $roles = Role::all();
        $moduleRoles = $module->roles->mapWithKeys(function ($item) {
            return [$item->getKey() => $item->toArray()];
        });

        return view('workflow::module.edit', compact('module', 'roles', 'moduleRoles'));
    }

    public function update($id)
    {
        $module = Module::findOrFail($id);

        DB::transaction(function () use ($module) {
            $module->update(request()->only('label'));
            $roles = collect(request()->input('roles', []))->filter(function ($item) {
                return Arr::has($item, 'id');
            })->transform(function ($item) {
                unset($item['id']);

                return $item;
            });
            $module->roles()->sync($roles);
        });

        return redirect()->back()->withSuccess('Module berhasil diupdate');
    }
}
