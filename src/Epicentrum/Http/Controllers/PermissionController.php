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
            $permissionModel = app(config('laravolt.epicentrum.models.permission'));
            $connection = $permissionModel->getConnection();
            $grammar = $connection->getQueryGrammar();

            // properly apply table prefix
            $table = $grammar->wrapTable($permissionModel->getTable());
            $keyName = $grammar->wrap($permissionModel->getKeyName());
            $descriptionColumn = $grammar->wrap('description');

            // Chunk data to avoid exceeding database driver parameter limits
            $chunkedUpdates = array_chunk($permissions, 300, true);

            foreach ($chunkedUpdates as $chunk) {
                $cases = [];
                $ids = [];
                $params = [];

                foreach ($chunk as $key => $description) {
                    $cases[] = "WHEN ? THEN ?";
                    $params[] = $key;
                    $params[] = $description;
                    $ids[] = $key;
                }

                $idsStr = implode(',', array_fill(0, count($ids), '?'));
                $sql = "UPDATE {$table} SET {$descriptionColumn} = CASE {$keyName} " . implode(' ', $cases) . " END";

                if ($permissionModel->usesTimestamps()) {
                    $updatedAtColumn = $grammar->wrap($permissionModel->getUpdatedAtColumn() ?? 'updated_at');
                    $now = $permissionModel->freshTimestampString();
                    $sql .= ", {$updatedAtColumn} = ?";
                    $params[] = $now;
                }

                $sql .= " WHERE {$keyName} IN ($idsStr)";
                $params = array_merge($params, $ids);

                $connection->update($sql, $params);
            }
        }

        return redirect()->back()->withSuccess('Permission updated');
    }
}
