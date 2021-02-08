<?php

namespace App\Services\Topic\Validator;


use App\Entities\User;
use App\Exceptions\WebException;
use Carbon\Carbon;

class UserValidator
{
    public function validate(User $user)
    {
        $verifiedColdDate = $this->getColdDownDate($user);
        if ($verifiedColdDate->gt(Carbon::now())) {
            throw new WebException("You can do it after email verified after {$verifiedColdDate}");
        }
    }

    public function isUserColdDown(User $user)
    {
        $verifiedColdDate = $this->getColdDownDate($user);
        return $verifiedColdDate->lt(Carbon::now());
    }

    public function getColdDownDate(User $user): Carbon
    {
        $userMustVerifiedAfter = config('hustoj.user.topic.verified_after');
        return $user->email_verified_at->addHours($userMustVerifiedAfter);
    }
}
