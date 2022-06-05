<?php

namespace App\Http\Controllers\My;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Laravolt\Epicentrum\Http\Requests\My\Profile\Update;
use Laravolt\Support\Contracts\TimezoneRepository;

class ProfileController extends Controller
{
    public function edit(TimezoneRepository $timezone): View
    {
        $user = auth()->user();
        $timezones = $timezone->all();

        return view('my.profile.edit', compact('user', 'timezones'));
    }

    public function update(Update $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->update($request->validated());

        return redirect()->back()->withSuccess(__('Profil berhasil diperbarui') ?? '');
    }
}
