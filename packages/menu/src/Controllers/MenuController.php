<?php

declare(strict_types=1);

namespace Laravolt\Menu\Controllers;

use App\Services\MenuExport;
use Illuminate\Routing\Controller;
use Laravolt\Menu\Enum\Color;
use Laravolt\Menu\Enum\UrlType;
use Laravolt\Menu\Models\Menu;
use Laravolt\Menu\Requests\Menu\Store;
use Laravolt\Menu\Requests\Menu\Update;
use Laravolt\Menu\TableView\MenuTableView;
use Laravolt\Platform\Models\Permission;
use Laravolt\Platform\Models\Role;
use Maatwebsite\Excel\Excel;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::withDepth()->ordered()->search(request('search'))->get()->toFlatTree();

        return (new MenuTableView($menu))
            ->view('menu::menu.index');
    }

    public function create()
    {
        $parent = Menu::toFlatSelect();
        $type = UrlType::toSelectArray();
        $permissions = Permission::pluck('name', 'name');
        $roles = Role::pluck('name', 'id');
        $colors = Color::toArray();
        $menu = new Menu();

        return view('menu::menu.create', compact('parent', 'menu', 'type', 'permissions', 'roles', 'colors'));
    }

    public function store(Store $request)
    {
        Menu::create($request->validated());

        return redirect()->back()->withSuccess(__('Menu berhasil ditambah'));
    }

    public function edit(Menu $menu)
    {
        $parent = Menu::toFlatSelect();
        $type = UrlType::toSelectArray();
        $permissions = Permission::pluck('name', 'name');
        $roles = Role::pluck('name', 'id');
        $colors = Color::toArray();

        return view('menu::menu.edit', compact('parent', 'menu', 'type', 'permissions', 'roles', 'colors'));
    }

    public function update(Menu $menu, Update $request)
    {
        $menu->update($request->validated() + ['roles' => $request->input('roles', [])]);

        return redirect()->back()->withSuccess(__('Menu berhasil diedit'));
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('menu::menu.index')->withSuccess(__('Menu berhasil dihapus'));
    }

    public function download()
    {
        $filename = sprintf('simpel-menu-%s.csv', now());

        return (new MenuExport())->download($filename, Excel::CSV);
    }
}
