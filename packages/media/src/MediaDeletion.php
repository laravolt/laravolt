<?php

declare(strict_types=1);

namespace Laravolt\Media;

use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Who may delete a media record through the media endpoints.
 *
 * The signed-in user may delete media they own (what they just uploaded).
 * Anything else needs the app's own "delete" ability for the media model,
 * for example a MediaPolicy. Guests may not delete media.
 */
final class MediaDeletion
{
    public static function allows(Media $media): bool
    {
        $user = auth()->user();

        if ($user === null) {
            return false;
        }

        $ownsIt = $media->model_type === $user->getMorphClass()
            && (string) $media->model_id === (string) $user->getAuthIdentifier();

        return $ownsIt || Gate::forUser($user)->allows('delete', $media);
    }
}
