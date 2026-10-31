<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Media owned by the Guest model.
 *
 * Guest is a Sushi model that lives in its own in-memory database, and the
 * media library copies the owner's connection onto new media. Without this,
 * every guest upload looked for the media table in Guest's database.
 */
class GuestMedia extends Media
{
    protected $table = 'media';

    public function getConnectionName()
    {
        return parent::getConnectionName() ?? config('database.default');
    }
}
