<?php

namespace App\Services\Topic\Validator;

use App\Entities\User;
use App\Exceptions\WebException;
use Carbon\Carbon;

class UserValidator
{
    /**
     * @throws WebException
     */
    public function validate(User $user): void
    {
        $verifiedColdDate = $this->getColdDownDate($user);
        if ($this->isColdDown($verifiedColdDate)) {
            throw new WebException("You can do it after email verified after {$verifiedColdDate}");
        }
    }

    public function isColdDown(Carbon $date): bool
    {
        return $date->lt(Carbon::now());
    }

    public function isUserColdDown(User $user): bool
    {
        $verifiedColdDate = $this->getColdDownDate($user);

        return $this->isColdDown($verifiedColdDate);
    }

    public function getColdDownDate(User $user): Carbon
    {
        $userMustVerifiedAfter = config('hustoj.user.topic.verified_after');
        if ($user->email_verified_at == null) {
            return Carbon::now()->addHour();
        }

        return $user->email_verified_at->addHours($userMustVerifiedAfter);
    }
}
