<?php

namespace Tests\Feature\Password;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CanChangePasswordTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_password_changed_at_filled()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->password_changed_at);
    }

    public function test_can_change_password()
    {
        $user = User::factory()->create();

        $user->setPassword('secret2', true);
        $this->assertNull($user->password_changed_at);

        $user->setPassword('secret2', false);
        $this->assertNotNull($user->password_changed_at);

        $this->assertTrue(Hash::check('secret2', $user->password));
    }

    public function test_password_must_be_changed()
    {
        $user = User::factory()->create();

        $user->setPassword('secret2', true);
        $this->assertTrue($user->passwordMustBeChanged(1));

        $user->setPassword('secret2', false);
        $this->assertFalse($user->passwordMustBeChanged(1));

        $user->setPassword('secret2', false);
        $this->assertFalse($user->passwordMustBeChanged(null));

        $user->setPassword('secret2', true);
        $this->assertTrue($user->passwordMustBeChanged(null));
    }

    public function test_password_must_be_changed_duration()
    {
        $user = User::factory()->create();

        // Lets assume user changed their password 2 days ago,
        $user->password_changed_at = Carbon::now()->subDays(2);

        // So, when we have password duration = 2 days, user must change their password
        // because it is already equal with the limit
        $this->assertTrue($user->passwordMustBeChanged(2));

        // But, if we have password duration = 3 days, user still allowed to use their passwrod
        // because it has 1 day remaining.
        $this->assertFalse($user->passwordMustBeChanged(3));
    }
}
