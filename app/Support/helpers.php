<?php

use App\Entities\Contest;
use App\Entities\Option;
use App\Entities\Solution;
use App\Entities\User;
use App\Services\ContestService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

if (!function_exists('get_option')) {
    function get_option($name, $default = null)
    {
        /** @var Collection|Option[] $options */
        $options = Option::all();
        foreach ($options as $option) {
            if ($option->key === $name && $option->value) {
                return $option->value;
            }
        }

        return $default;
    }
}

if (!function_exists('can_attend')) {
    /**
     * @param Contest $contest
     *
     * @return bool
     */
    function can_attend($contest)
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        if ($user->hasRole('admin')) {
            return true;
        }

        return $contest->users()->wherePivot('user_id', '=', $user->id)->get()->count();
    }
}

if (!function_exists('show_ratio')) {
    function show_ratio($number, $total)
    {
        $result = 0;
        if ($total > 0) {
            $result = 100.0 * $number / $total;
        }

        return sprintf('%2.2f%%', $result);
    }
}

if (!function_exists('display_penalize_time')) {
    function display_penalize_time($seconds)
    {
        $hour = (int) ($seconds / (Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR));
        $seconds -= $hour * Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR;
        $minute = (int) ($seconds / Carbon::SECONDS_PER_MINUTE);
        $leftSeconds = $seconds % Carbon::SECONDS_PER_MINUTE;

        return sprintf('%d:%02d:%02d', $hour, $minute, $leftSeconds);
    }
}

if (!function_exists('show_order')) {
    function show_order($order)
    {
        return chr($order + ord('A'));
    }
}

if (!function_exists('show_problem_id')) {
    /**
     * @param Solution $solution
     *
     * @return int|string
     */
    function show_problem_id($solution)
    {
        if ($solution->contest_id) {
            return show_order($solution->order);
        }

        return $solution->problem_id;
    }
}

if (!function_exists('opening_contest')) {
    function opening_contest()
    {
        return (new ContestService())->openingContest();
    }
}

if (!function_exists('original_order')) {
    function original_order($order)
    {
        return ord($order) - ord('A');
    }
}

if (!function_exists('!show_status')) {
    function show_status($status)
    {
        if (array_key_exists($status, Solution::$status)) {
            return Solution::$status[$status];
        }

        return 'not found!';
    }
}
