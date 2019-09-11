<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Password;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravolt\Tests\FeatureTest;

class CanChangePasswordTest extends FeatureTest
{
    public function testPasswordChangedAtFilled()
    {
        $user = $this->createUser();
        $this->assertNotNull($user->password_changed_at);
    }

    public function testCanChangePassword()
    {
        $user = $this->createUser();

        $user->setPassword('secret2', true);
        $this->assertNull($user->password_changed_at);

        $user->setPassword('secret2', false);
        $this->assertNotNull($user->password_changed_at);

        $this->assertTrue(Hash::check('secret2', $user->password));
    }

    public function testPasswordMustBeChanged()
    {
        $user = $this->createUser();

        $user->setPassword('secret2', true);
        $this->assertTrue($user->passwordMustBeChanged(1));

        $user->setPassword('secret2', false);
        $this->assertFalse($user->passwordMustBeChanged(1));

        $user->setPassword('secret2', false);
        $this->assertFalse($user->passwordMustBeChanged(null));

        $user->setPassword('secret2', true);
        $this->assertTrue($user->passwordMustBeChanged(null));
    }

    public function testPasswordMustBeChangedDuration()
    {
        $user = $this->createUser();

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
