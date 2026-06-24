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
            /** @var \Illuminate\Database\Eloquent\Model $model */
            $model = new $modelClass();
            $connection = $model->getConnection();
            $grammar = $connection->getQueryGrammar();

            $table = $grammar->wrapTable($model->getTable());
            $idColumn = $grammar->wrap($model->getKeyName());
            $descColumn = $grammar->wrap('description');

            $updatedAtColumn = $model->usesTimestamps() && ! is_null($model->getUpdatedAtColumn())
                ? $grammar->wrap($model->getUpdatedAtColumn())
                : null;

            // Process in chunks to avoid database parameter limits
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

                $casesSql = implode(' ', $cases);
                $placeholders = implode(', ', array_fill(0, count($ids), '?'));

                $sql = "UPDATE {$table} SET {$descColumn} = CASE {$casesSql} ELSE {$descColumn} END";

                if ($updatedAtColumn) {
                    $sql .= ", {$updatedAtColumn} = ?";
                    $bindings[] = $model->freshTimestampString();
                }

                $sql .= " WHERE {$idColumn} IN ({$placeholders})";
                $bindings = array_merge($bindings, $ids);

                $connection->update($sql, $bindings);
            }
        }

        return redirect()->back()->withSuccess('Permission updated');
    }
}
