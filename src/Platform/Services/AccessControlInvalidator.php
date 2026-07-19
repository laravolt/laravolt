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
    /**
     * @var bool|null
     */
    protected $hasValidSessionTable = null;

    public function invalidateUser(Model $user): void
    {
        $userId = $user->getKey();

        Cache::forget("users.{$userId}.permissions");
        $this->deleteDatabaseSessions([$userId]);
    }

    public function invalidateUsers(iterable $users): void
    {
        $userIds = [];

        foreach ($users as $user) {
            if ($user instanceof Model) {
                $userId = $user->getKey();
                $userIds[] = $userId;
                Cache::forget("users.{$userId}.permissions");
            }
        }

        if (! empty($userIds)) {
            $this->deleteDatabaseSessions($userIds);
        }
    }

    /**
     * @param array $userIds
     */
    protected function deleteDatabaseSessions(array $userIds): void
    {
        try {
            $connection = config('session.connection');
            $table = config('session.table', 'sessions');

            if ($this->hasValidSessionTable === null) {
                $schema = Schema::connection($connection);
                $this->hasValidSessionTable = $schema->hasTable($table) && $schema->hasColumn($table, 'user_id');
            }

            if (! $this->hasValidSessionTable) {
                return;
            }

            DB::connection($connection)->table($table)->whereIn('user_id', $userIds)->delete();
        } catch (Throwable) {
            $this->hasValidSessionTable = false;
            // Session invalidation is best-effort because Laravolt can run with
            // non-database session drivers or applications without session table.
        }
    }
}
