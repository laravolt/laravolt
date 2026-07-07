<?php

declare(strict_types=1);

namespace Laravolt\Epicentrum\Http\Controllers;

use Illuminate\Routing\Controller;

class PermissionController extends Controller
{
    public function edit()
    {
        $permissions = config('laravolt.epicentrum.models.permission')::all()->sortBy(
            function ($item) {
                return mb_strtolower($item->name);
            }
        );

        return view('laravolt::permissions.edit', compact('permissions'));
    }

    public function update()
    {
        $permissions = request('permission', []);

        if (empty($permissions)) {
            return redirect()->back()->withSuccess('Permission updated');
        }

        $modelClass = config('laravolt.epicentrum.models.permission');
        $model = app($modelClass);

        $keyName = $model->getKeyName();
        $builder = $model->newModelQuery();
        $grammar = $builder->getQuery()->getGrammar();

        $cases = [];
        $bindings = [];
        $ids = [];

        foreach ($permissions as $id => $description) {
            $cases[] = "WHEN {$grammar->wrap($keyName)} = ? THEN ?";
            $bindings[] = $id;
            $bindings[] = $description;
            $ids[] = $id;
        }

        $wrappedTable = $grammar->wrapTable($model->getTable());
        $wrappedKeyName = $grammar->wrap($keyName);
        $wrappedDescription = $grammar->wrap('description');
        $placeholders = implode(', ', array_fill(0, count($ids), '?'));

        $casesSql = implode(' ', $cases);

        if ($model->usesTimestamps() && ! is_null($model->getUpdatedAtColumn())) {
            $updatedAtColumn = $model->getUpdatedAtColumn();
            $wrappedUpdatedAt = $grammar->wrap($updatedAtColumn);
            $rawSql = "UPDATE {$wrappedTable} SET {$wrappedDescription} = CASE {$casesSql} ELSE {$wrappedDescription} END, {$wrappedUpdatedAt} = ? WHERE {$wrappedKeyName} IN ({$placeholders})";
            $bindings[] = $model->freshTimestampString();
        } else {
            $rawSql = "UPDATE {$wrappedTable} SET {$wrappedDescription} = CASE {$casesSql} ELSE {$wrappedDescription} END WHERE {$wrappedKeyName} IN ({$placeholders})";
        }

        $builder->getConnection()->update($rawSql, array_merge($bindings, $ids));

        return redirect()->back()->withSuccess('Permission updated');
    }
}
