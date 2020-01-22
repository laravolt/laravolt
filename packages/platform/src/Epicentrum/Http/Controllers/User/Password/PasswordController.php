<?php

namespace Laravolt\Epicentrum\Http\Controllers\User\Password;

use Illuminate\Routing\Controller;
use Laravolt\Epicentrum\Repositories\RepositoryInterface;

class PasswordController extends Controller
{
    /**
     * @var UserRepositoryEloquent
     */
    private $repository;

    /**
     * PasswordController constructor.
     *
     * @param UserRepositoryEloquent $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

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

        return view('laravolt::password.edit', compact('user'));
    }
}
