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

        if (! empty($permissions)) {
            $modelClass = config('laravolt.epicentrum.models.permission');
            $model = app($modelClass);
            $connection = $model->getConnection();
            $grammar = $connection->getQueryGrammar();

            $table = $grammar->wrapTable($model->getTable());
            $keyName = $model->getKeyName();
            $wrappedKeyName = $grammar->wrap($keyName);
            $wrappedDescColumn = $grammar->wrap('description');

            $hasTimestamps = $model->usesTimestamps() && ! is_null($model->getUpdatedAtColumn());
            $wrappedUpdatedAt = $hasTimestamps ? $grammar->wrap($model->getUpdatedAtColumn()) : null;

            // Chunk to avoid exceeding maximum bound variables limits (especially on SQLite)
            foreach (array_chunk($permissions, 200, true) as $chunk) {
                $cases = [];
                $bindings = [];
                $ids = [];

                foreach ($chunk as $key => $description) {
                    $cases[] = "WHEN {$wrappedKeyName} = ? THEN ?";
                    // Cast key to string to handle both ULID and Integer safely.
                    $bindings[] = (string) $key;
                    $bindings[] = $description;
                    $ids[] = (string) $key;
                }

                $placeholders = implode(', ', array_fill(0, count($ids), '?'));

                $sql = "UPDATE {$table} SET {$wrappedDescColumn} = CASE "
                    . implode(' ', $cases)
                    . " ELSE {$wrappedDescColumn} END";

                if ($hasTimestamps) {
                    $sql .= ", {$wrappedUpdatedAt} = ?";
                    $bindings[] = $model->freshTimestampString();
                }

                $sql .= " WHERE {$wrappedKeyName} IN ({$placeholders})";

                $bindings = array_merge($bindings, $ids);
                $connection->update($sql, $bindings);
            }
        }

        return redirect()->back()->withSuccess('Permission updated');
    }
}
