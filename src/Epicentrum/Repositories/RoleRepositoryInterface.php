<?php

namespace Laravolt\Epicentrum\Repositories;

/**
 * Interface UserRepository.
 */
interface RoleRepositoryInterface
{
    public function findById($id);

    public function all();

    public function create(array $attributes);

    public function update($id, array $attributes);

    public function delete($id);
}
