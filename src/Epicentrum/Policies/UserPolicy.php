<?php

declare(strict_types=1);

namespace Laravolt\Epicentrum\Policies;

use Illuminate\Auth\Access\Response;
use Laravolt\Epicentrum\Repositories\RepositoryInterface;

class UserPolicy
{
    private RepositoryInterface $repository;

    /**
     * UserPolicy constructor.
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create()
    {
        $userLimit = (int) config('laravolt.epicentrum.user_limit');
        $currentUser = $this->repository->count();
        if (($userLimit > 0) && $currentUser >= $userLimit) {
            return Response::deny("Reached maximum user limit: $userLimit");
        }

        return Response::allow();
    }
}
