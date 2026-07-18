<?php

declare(strict_types=1);

namespace Laravolt\Epicentrum\Http\Controllers;

use Illuminate\Routing\Controller;

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
        $permissions = request('permission', []);

        if (!empty($permissions)) {
            $modelClass = config('laravolt.epicentrum.models.permission');
            $model = new $modelClass();
            $connection = $model->getConnection();
            $grammar = $connection->getQueryGrammar();
            $table = $grammar->wrapTable($model->getTable());
            $idColumn = $grammar->wrap($model->getKeyName());
            $descColumn = $grammar->wrap('description');

            $updatedAtColumn = $model->getUpdatedAtColumn();
            $hasUpdatedAt = $model->usesTimestamps() && !is_null($updatedAtColumn);
            $updatedAtColumnWrapped = $hasUpdatedAt ? $grammar->wrap($updatedAtColumn) : null;

            // Chunking the data to prevent hitting database parameter limits
            foreach (array_chunk($permissions, 300, true) as $chunk) {
                $cases = [];
                $bindings = [];
                $ids = [];

                foreach ($chunk as $id => $description) {
                    $cases[] = "WHEN {$idColumn} = ? THEN ?";
                    $bindings[] = $id;
                    $bindings[] = $description;
                    $ids[] = $id;
                }

                $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));

                $query = "UPDATE {$table} SET {$descColumn} = CASE " . implode(' ', $cases) . " ELSE {$descColumn} END";

                if ($hasUpdatedAt) {
                    $query .= ", {$updatedAtColumnWrapped} = ?";
                    $bindings[] = $model->freshTimestampString();
                }

                $query .= " WHERE {$idColumn} IN ({$idPlaceholders})";
                $bindings = array_merge($bindings, $ids);

                $connection->statement($query, $bindings);
            }
        }

        return redirect()->back()->withSuccess('Permission updated');
    }
}
