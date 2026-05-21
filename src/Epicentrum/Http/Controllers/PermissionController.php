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

        if (! empty($permissions)) {
            $modelClass = config('laravolt.epicentrum.models.permission');
            $model = new $modelClass();
            $connection = $model->getConnectionName() ?: config('database.default');

            // ⚡ Bolt: Optimizing O(N) queries to O(1) by batching with CASE statement
            // We chunk in case of numerous permissions to avoid database driver parameter limits
            foreach (array_chunk($permissions, 300, true) as $chunk) {
                $cases = [];
                $params = [];
                $ids = [];

                $keyName = $model->getKeyName();

                foreach ($chunk as $key => $description) {
                    $cases[] = "WHEN {$keyName} = ? THEN ?";
                    $params[] = $key;
                    $params[] = $description;
                    $ids[] = $key;
                }

                $hasTimestamps = $model->usesTimestamps();
                if ($hasTimestamps) {
                    $params[] = now(); // For updated_at
                }

                $idsString = implode(',', array_fill(0, count($ids), '?'));
                $params = array_merge($params, $ids);

                $casesSql = implode(' ', $cases);

                $grammar = $model->getConnection()->getQueryGrammar();
                $wrappedTable = $grammar->wrapTable($model->getTable());
                $wrappedDescription = $grammar->wrap('description');
                $wrappedKey = $grammar->wrap($keyName);

                $setUpdatedAt = $hasTimestamps ? ", {$grammar->wrap($model->getUpdatedAtColumn())} = ?" : '';

                $sql = "UPDATE {$wrappedTable} SET {$wrappedDescription} = CASE {$casesSql} ELSE {$wrappedDescription} END{$setUpdatedAt} WHERE {$wrappedKey} IN ({$idsString})";

                \Illuminate\Support\Facades\DB::connection($connection)->update($sql, $params);
            }
        }

        return redirect()->back()->withSuccess('Permission updated');
    }
}
