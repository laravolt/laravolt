<?php

namespace App\Http\Controllers\My;

use Illuminate\Routing\Controller;
use Laravolt\Epicentrum\Http\Requests\My\Profile\Update;
use Laravolt\Support\Contracts\TimezoneRepository;

class ProfileController extends Controller
{
    private $timezone;

    /**
     * PasswordController constructor.
     *
     * @param TimezoneRepository $timezone
     */
    public function __construct(TimezoneRepository $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit()
    {
        $user = auth()->user();
        $timezones = $this->timezone->all();

        return view('my.profile.edit', compact('user', 'timezones'));
    }

    public function update(Update $request)
    {
        $user = auth()->user();
        $user->update($request->validated());

        return redirect()->back()->withSuccess(__('Profil berhasil diperbarui'));
    }
}
