<?php

declare(strict_types=1);

namespace App\Http\Controllers\My;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Laravolt\Epicentrum\Http\Requests\My\Profile\Update;
use Laravolt\Support\Contracts\TimezoneRepository;

final class ProfileController extends Controller
{
    public function edit(TimezoneRepository $timezone): View
    {
        $user = auth()->user();
        $timezones = $timezone->all();

        return view('my.profile.edit', ['user' => $user, 'timezones' => $timezones]);
    }

    public function update(Update $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        $user->update($validated);

        return back()->withSuccess(__('Profil berhasil diperbarui'));
    }
}
