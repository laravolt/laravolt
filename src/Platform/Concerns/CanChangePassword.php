<?php

declare(strict_types=1);

namespace Laravolt\Platform\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait CanChangePassword
{
    /**
     * @param  bool  $mustBeChanged
     * @return $this
     */
    public function setPassword($password, $mustBeChanged = false): bool
    {
        $this->password = bcrypt($password);

        if ($mustBeChanged) {
            $this->password_changed_at = null;
        }

        return $this->save();
    }

    public function setPasswordAttribute($value): void
    {
        if ($value) {
            if (strlen($value) === 60 && preg_match('/^\$2y\$/', $value)) {
                $this->attributes['password'] = $value;
            } else {
                $this->attributes['password'] = Hash::make($value);
            }

            $this->password_changed_at = new Carbon;
        }
    }

    /**
     * @param  null  $durationInDays
     */
    public function passwordMustBeChanged($durationInDays = null): bool
    {
        if ($this->password_changed_at === null) {
            return true;
        }

        if ($durationInDays === null) {
            return false;
        }

        return $this->password_changed_at->addDays((int) $durationInDays)->lte(Carbon::now());
    }

    public function getEmailForNewPassword(): string
    {
        return $this->email;
    }
}
