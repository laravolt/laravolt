<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Ui;

use Illuminate\Support\Str;
use Laravolt\Platform\Models\User;
use Laravolt\Tests\FeatureTest;

class UserAvatarTest extends FeatureTest
{
    public function test_an_avatar_is_generated_once_per_name(): void
    {
        $generated = [];
        $this->app->bind('avatar', function () use (&$generated) {
            return new class($generated)
            {
                private string $name = '';

                public function __construct(private array &$generated) {}

                public function create($name): static
                {
                    $this->name = (string) $name;

                    return $this;
                }

                public function toBase64(): string
                {
                    $this->generated[] = $this->name;

                    return 'data:image/png;base64,'.base64_encode($this->name);
                }
            };
        });

        // The memo outlives the test (it is per process), so use fresh names.
        $ani = 'Ani '.Str::random(8);
        $budi = 'Budi '.Str::random(8);

        $first = (new User)->forceFill(['name' => $ani])->avatar;
        $again = (new User)->forceFill(['name' => $ani])->avatar;
        $other = (new User)->forceFill(['name' => $budi])->avatar;

        $this->assertSame($first, $again);
        $this->assertSame('data:image/png;base64,'.base64_encode($budi), $other);
        $this->assertSame([$ani, $budi], $generated);

        // Another avatar config is another avatar.
        config(['laravolt.avatar.shape' => 'square-'.Str::random(4)]);
        (new User)->forceFill(['name' => $ani])->avatar;
        $this->assertSame([$ani, $budi, $ani], $generated);
    }
}
