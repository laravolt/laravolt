<?php

namespace Laravolt\Epicentrum\Http\Controllers\User;

use Laravolt\Epicentrum\Contracts\Requests\Account\Update;

class AccountController extends UserController
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->repository->findById($id);
        $statuses = $this->repository->availableStatus();
        $timezones = $this->timezone->all();
        $roles = app('laravolt.epicentrum.role')->all();
        $multipleRole = config('laravolt.epicentrum.role.multiple');
        $roleEditable = config('laravolt.epicentrum.role.editable');

        return view('laravolt::account.edit', compact('user', 'statuses', 'timezones', 'roles', 'multipleRole', 'roleEditable'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, $id)
    {
        try {
            $this->repository->updateAccount($id, $request->except('_token', '_method'), $request->get('roles', []));

            return redirect()->back()->withSuccess(trans('laravolt::message.account_updated'));
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }
}
