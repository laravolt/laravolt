<?php

namespace Laravolt\Epicentrum\Http\Controllers\My;

use Illuminate\Routing\Controller;
use Laravolt\Epicentrum\Http\Requests\My\Profile\Update;
use Laravolt\Epicentrum\Repositories\RepositoryInterface;
use Laravolt\Support\Contracts\TimezoneRepository;

class ProfileController extends Controller
{
    /**
     * @var UserRepositoryEloquent
     */
    private $repository;

    private $timezone;

    /**
     * PasswordController constructor.
     *
     * @param RepositoryInterface $repository
     * @param TimezoneRepository  $timezone
     */
    public function __construct(RepositoryInterface $repository, TimezoneRepository $timezone)
    {
        $this->repository = $repository;
        $this->timezone = $timezone;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user = auth()->user();
        $timezones = $this->timezone->all();

        return view('laravolt::my.profile.edit', compact('user', 'timezones'));
    }

    public function update(Update $request)
    {
        $user = auth()->user();
        $user->update($request->validated());

        return redirect()->back()->withSuccess(__('Profil berhasil diperbarui'));
    }
}
