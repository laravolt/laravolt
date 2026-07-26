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

        if (!empty($permissions)) {
            $modelClass = config('laravolt.epicentrum.models.permission');
            $model = new $modelClass();
            $connection = $model->getConnection();
            $grammar = $connection->getQueryGrammar();

            $table = $grammar->wrapTable($model->getTable());
            $idColumn = $grammar->wrap($model->getKeyName());
            $descColumn = $grammar->wrap('description');

            $usesTimestamps = $model->usesTimestamps() && !is_null($model->getUpdatedAtColumn());
            $updatedAtColumn = $usesTimestamps ? $grammar->wrap($model->getUpdatedAtColumn()) : null;

            $chunks = array_chunk($permissions, 300, true);

            foreach ($chunks as $chunk) {
                $cases = [];
                $bindings = [];
                $ids = [];

                foreach ($chunk as $key => $description) {
                    $cases[] = "WHEN {$idColumn} = ? THEN ?";
                    $bindings[] = $key;
                    $bindings[] = $description;
                    $ids[] = $key;
                }

                $casesSql = implode(' ', $cases);
                $setSql = "{$descColumn} = CASE {$casesSql} ELSE {$descColumn} END";

                if ($usesTimestamps) {
                    $setSql .= ", {$updatedAtColumn} = ?";
                    $bindings[] = $model->freshTimestampString();
                }

                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $bindings = array_merge($bindings, $ids);

                $query = "UPDATE {$table} SET {$setSql} WHERE {$idColumn} IN ({$placeholders})";

                $connection->update($query, $bindings);
            }
        }

        return redirect()->back()->withSuccess('Permission updated');
    }
}
