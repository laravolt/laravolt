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
            $grammar = $model->getConnection()->getQueryGrammar();

            $table = $grammar->wrapTable($model->getTable());
            $keyName = $grammar->wrap($model->getKeyName());
            $descColumn = $grammar->wrap('description');

            // Chunk to avoid hitting SQLite/database parameter limits. Each permission adds 3 bindings.
            foreach (array_chunk($permissions, 300, true) as $chunk) {
                $cases = [];
                $bindings = [];
                $ids = [];

                foreach ($chunk as $key => $description) {
                    $cases[] = 'WHEN ? THEN ?';
                    $bindings[] = $key;
                    $bindings[] = $description;
                    $ids[] = $key;
                }

                $casesString = implode(' ', $cases);
                $setClause = "{$descColumn} = CASE {$keyName} {$casesString} END";

                if ($model->usesTimestamps() && !is_null($model->getUpdatedAtColumn())) {
                    $updatedAtColumn = $grammar->wrap($model->getUpdatedAtColumn());
                    $setClause .= ", {$updatedAtColumn} = ?";
                    $bindings[] = $model->freshTimestampString();
                }

                $bindings = array_merge($bindings, $ids);
                $placeholders = implode(', ', array_fill(0, count($ids), '?'));

                $sql = "UPDATE {$table} SET {$setClause} WHERE {$keyName} IN ({$placeholders})";

                $model->getConnection()->update($sql, $bindings);
            }
        }

        return redirect()->back()->withSuccess('Permission updated');
    }
}
