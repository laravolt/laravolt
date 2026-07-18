<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\My;

use Laravolt\Platform\Models\User;
use Laravolt\Tests\FeatureTest;

class UserAvatarTest extends FeatureTest
{
    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            \Laravolt\Avatar\ServiceProvider::class,
        ]);
    }

    public function test_avatar()
    {
        $user = new User(["name" => "Admin"]);
        $avatar = $user->avatar;
        echo substr($avatar, 0, 50) . "...";
        $this->assertNotNull($avatar);
    }
}
