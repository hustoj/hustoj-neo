<?php

use App\Entities\Contest;
use App\Entities\Solution;
use App\Entities\User;
use App\Services\ContestService;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

if (! function_exists('new_judge_code')) {
    function new_judge_code(): \Ramsey\Uuid\UuidInterface
    {
        return Uuid::getFactory()->uuid4();
    }
}

if (! function_exists('captcha_enabled')) {
    function captcha_enabled(): bool
    {
        return config('captcha.enabled');
    }
}

if (! function_exists('is_alpha')) {
    /**
     * detect is only alpha string.
     *
     * @param  $s
     * @return bool
     */
    function is_alpha($s): bool
    {
        return preg_match('/^[a-zA-Z]+$/', $s);
    }
}

if (! function_exists('current_page')) {
    function current_page($name = 'page', $default = 1)
    {
        return \Illuminate\Pagination\Paginator::resolveCurrentPage($name, $default);
    }
}

if (! function_exists('where_like')) {
    function where_like($term): string
    {
        return sprintf('%%%s%%', $term);
    }
}

if (! function_exists('get_option')) {
    function get_option($name, $default = null)
    {
        return app(\App\Services\OptionProvider::class)->getOption($name, $default);
    }
}

if (! function_exists('can_attend')) {
    /**
     * @param  Contest  $contest
     * @return bool
     */
    function can_attend(Contest $contest): bool
    {
        /** @var User $user */
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('admin')) {
            return true;
        }

        return $contest->users()->wherePivot('user_id', '=', $user->id)->get()->count();
    }
}

if (! function_exists('contest_permission')) {
    /**
     * @param  Contest  $contest
     * @return string
     */
    function contest_permission($contest)
    {
        if ($contest instanceof Contest) {
            $contest = $contest->id;
        }

        return 'contest.'.$contest;
    }
}

if (! function_exists('can_view_code')) {
    function can_view_code($solution)
    {
        /** @var User $user */
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        /** @var \App\Services\AdminChecker $adminChecker */
        $adminChecker = app(\App\Services\AdminChecker::class);
        if ($adminChecker->isAdmin($user)) {
            return true;
        }

        return $solution->user_id == $user->id;
    }
}

if (! function_exists('show_ratio')) {
    function show_ratio($number, $total)
    {
        $result = 0;
        if ($total > 0) {
            $result = 100.0 * $number / $total;
        }

        return sprintf('%2.2f%%', $result);
    }
}

if (! function_exists('display_penalize_time')) {
    function display_penalize_time($seconds)
    {
        $hour = (int) ($seconds / (Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR));
        $seconds -= $hour * Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR;
        $minute = (int) ($seconds / Carbon::SECONDS_PER_MINUTE);
        $leftSeconds = $seconds % Carbon::SECONDS_PER_MINUTE;

        return sprintf('%d:%02d:%02d', $hour, $minute, $leftSeconds);
    }
}

if (! function_exists('show_order')) {
    function show_order($order)
    {
        return chr($order + ord('A'));
    }
}

if (! function_exists('show_problem_id')) {
    /**
     * @param  Solution  $solution
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

if (! function_exists('opening_contest')) {
    function opening_contest()
    {
        return (new ContestService())->openingContest();
    }
}

if (! function_exists('original_order')) {
    function original_order($order)
    {
        $order = strtoupper($order);

        return ord($order) - ord('A');
    }
}

if (! function_exists('!show_status')) {
    function show_status($status)
    {
        if (array_key_exists($status, Solution::$status)) {
            return Solution::$status[$status];
        }

        return 'not found!';
    }
}
