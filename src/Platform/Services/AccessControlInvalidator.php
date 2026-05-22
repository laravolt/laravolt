<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AccessControlInvalidator
{
    public function invalidateUser(Model $user): void
    {
        $userId = $user->getKey();

        Cache::forget("users.{$userId}.permissions");
        $this->deleteDatabaseSessions($userId);
    }

    public function invalidateUsers(iterable $users): void
    {
        $userIds = [];

        foreach ($users as $user) {
            if ($user instanceof Model) {
                $userId = $user->getKey();
                // ⚡ Bolt: Cache doesn't support bulk invalidation, iterate here
                Cache::forget("users.{$userId}.permissions");
                $userIds[] = $userId;
            }
        }

        if (! empty($userIds)) {
            // ⚡ Bolt: Pass all IDs to bulk delete sessions and avoid N+1 schema checks
            $this->deleteDatabaseSessions($userIds);
        }
    }

    protected function deleteDatabaseSessions(mixed $userId): void
    {
        if (empty($userId)) {
            return;
        }

        try {
            $connection = config('session.connection');
            $table = config('session.table', 'sessions');
            $schema = Schema::connection($connection);

            if (! $schema->hasTable($table) || ! $schema->hasColumn($table, 'user_id')) {
                return;
            }

            $query = DB::connection($connection)->table($table);

            if (is_iterable($userId)) {
                $ids = is_array($userId) ? $userId : iterator_to_array($userId);
                // ⚡ Bolt: Chunk large arrays to prevent database parameter limit issues
                foreach (array_chunk($ids, 300) as $chunk) {
                    (clone $query)->whereIn('user_id', $chunk)->delete();
                }
            } else {
                $query->where('user_id', $userId)->delete();
            }
        } catch (Throwable) {
            // Session invalidation is best-effort because Laravolt can run with
            // non-database session drivers or applications without session table.
        }
    }
}
