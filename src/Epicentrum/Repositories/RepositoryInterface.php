<?php

declare(strict_types=1);

namespace Laravolt\Epicentrum\Repositories;

use Illuminate\Http\Request;

/**
 * Interface UserRepository.
 */
interface RepositoryInterface
{
    public function findById($id);

    public function paginate(Request $request);

    public function createByAdmin(array $attributes, $roles = null);

    public function updateAccount($id, $account, $roles);

    public function updatePassword($password, $id);

    public function delete($id);

    public function availableStatus();

    public function count(): int;
}
