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
        $model = new $modelClass();
        $table = $model->getTable();
        $keyName = $model->getKeyName();
        $connection = $model->getConnection();
        $grammar = $connection->getQueryGrammar();

        $wrappedTable = $grammar->wrapTable($table);
        $wrappedKey = $grammar->wrap($keyName);
        $wrappedDescription = $grammar->wrap('description');
        $updatedAtColumn = $model->getUpdatedAtColumn() ?? 'updated_at';
        $wrappedUpdatedAt = $grammar->wrap($updatedAtColumn);
        $timestamp = $model->freshTimestampString();

        // Chunk data to avoid exceeding database driver parameter limits
        foreach (array_chunk($permissions, 300, true) as $chunk) {
            $cases = [];
            $bindings = [];
            $ids = [];

            foreach ($chunk as $key => $description) {
                $cases[] = "WHEN {$wrappedKey} = ? THEN ?";
                $bindings[] = $key;
                $bindings[] = $description;
                $ids[] = $key;
            }

            $idsString = implode(', ', array_fill(0, count($ids), '?'));
            $bindings[] = $timestamp;
            $bindings = array_merge($bindings, $ids);

            $sql = "UPDATE {$wrappedTable} SET {$wrappedDescription} = CASE " . implode(' ', $cases) . " END, {$wrappedUpdatedAt} = ? WHERE {$wrappedKey} IN ({$idsString})";

            $connection->update($sql, $bindings);
        }

        return redirect()->back()->withSuccess('Permission updated');
    }
}
