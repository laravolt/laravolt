<?php

namespace Laravolt\Epicentrum\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class UserRepositoryEloquent.
 */
class EloquentRepository implements RepositoryInterface
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
        $this->model = app(config('auth.providers.users.model'));
        $this->fieldSearchable = config('laravolt.epicentrum.repository.searchable', []);
    }

    public function findById($id)
    {
        return $this->model->query()->findOrFail($id);
    }

    public function paginate(Request $request)
    {
        $query = $this->model->autoSort()->autoFilter()->latest();
        if (($search = $request->get('search')) !== null) {
            $query->whereLike($this->fieldSearchable, $search);
        }

        return $query->paginate();
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
    public function createByAdmin(array $attributes, $roles = null)
    {
        $attributes['password'] = bcrypt($attributes['password']);
        $user = $this->model->fill($attributes);

        if (Arr::has($attributes, 'must_change_password')) {
            $user->password_changed_at = null;
        }

        $user->save();
        $user->syncRoles($roles);

        return $user;
    }

    public function updateAccount($id, $account, $roles)
    {
        $user = $this->findById($id);
        $user->update($account);

        if (config('laravolt.epicentrum.role.editable')) {
            $user->roles()->sync($roles);
        }

        return $user;
    }

    public function updatePassword($password, $id)
    {
        $user = $this->skipPresenter()->find($id);
        $user->setPassword($password);

        return $user->save();
    }

    public function delete($id)
    {
        $model = $this->model->query()->findOrFail($id);

        if (in_array(SoftDeletes::class, class_uses($this->model))) {
            $model->email = sprintf('[deleted-%s]%s', $model->id, $model->email);
            $model->save();
        }

        return $model->delete();
    }

    public function availableStatus()
    {
        return config('laravolt.epicentrum.user_available_status');
    }
}
