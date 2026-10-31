<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Sushi\Sushi;

class Guest extends Model implements HasMedia
{
    use InteractsWithMedia;
    use Sushi;

    public function getMediaModel(): string
    {
        return GuestMedia::class;
    }

    public function getRows()
    {
        return [
            ['id' => 1],
        ];
    }
}
