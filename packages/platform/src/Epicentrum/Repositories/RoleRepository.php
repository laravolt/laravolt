<?php

namespace Laravolt\Epicentrum\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRepositoryEloquent.
 */
class RoleRepository implements RoleRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * Boot up the repository, pushing criteria.
     */
    public function __construct()
    {
        $this->model = app('laravolt.epicentrum.role');
        $this->fieldSearchable = config('laravolt.epicentrum.repository.searchable', []);
    }

    public function findById($id)
    {
        return $this->model->query()->findOrFail($id);
    }

    public function all()
    {
        return $this->model->with('users', 'permissions')->get();
    }

    /**
     * Save a new entity in repository.
     *
     * @param array $attributes
     * @param null  $roles
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(array $attributes)
    {
        $role = $this->model->create($attributes);
        $role->syncPermission($attributes['permissions'] ?? []);

        return $role;
    }

    public function update($id, array $attributes)
    {
        $role = $this->findById($id);
        $role->update($attributes);
        $role->syncPermission($attributes['permissions'] ?? []);

        return $role;
    }

    public function delete($id)
    {
        $model = $this->model->query()->findOrFail($id);

        return $model->delete($id);
    }
}
