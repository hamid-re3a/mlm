<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use MLM\Models\EmailContentSetting;
use User\Models\User;

if (!function_exists('subset')) {


    function subset(Illuminate\Support\Collection $collection, $set_number = 1, $chunk_size = 3, $from_node = 0, $sortBy = 'id')
    {
        return $collection->sortBy($sortBy)
            ->values()->filter(function ($item, $index) use ($set_number, $chunk_size, $from_node) {
                $start_index = $index - $from_node;
                return floor($start_index / $chunk_size) + 1 == $set_number;
            })->values()->toArray();
    }
}

if (!function_exists('getRank')) {

    function getRank($rank)
    {
        if (Illuminate\Support\Facades\DB::table('ranks')->exists()) {
            $key_db = \MLM\Models\Rank::query()->where('rank', $rank)->first();
            if ($key_db && !empty($key_db->value))
                return $key_db->toArray();
        }

        if (isset(RANKS[$rank]))
            return RANKS[$rank];

        return null;
    }
}


if (!function_exists('userRankBasedOnConvertedPoint')) {
    function userRankBasedOnConvertedPoint($converted_point): \MLM\Models\Rank
    {
        return \MLM\Models\Rank::query()->where('condition_converted_in_bp', '<=', $converted_point / BF_TO_BB_RATIO)->orderBy('rank', 'desc')->first();
    }
}
if (!function_exists('getAndUpdateUserRank')) {

    function getAndUpdateUserRank(\User\Models\User $user): ?\MLM\Models\Rank
    {
        $ranks = \MLM\Models\Rank::query()->orderBy('rank', 'desc')->get();
        foreach ($ranks as $rank) {
            if ($user->binaryTree->converted_points >= BF_TO_BB_RATIO * $rank->condition_converted_in_bp) {


                $left_binary_children = $user->binaryTree->leftSideChildrenIds();
                $right_binary_children = $user->binaryTree->rightSideChildrenIds();
                if ($rank->condition_direct_or_indirect) {
                    $referral_children = $user->referralTree->descendantsUserIds();
                } else {
                    $referral_children = $user->referralTree->childrenUserIds();
                }

                $left_binary_sponsored_children = array_intersect($left_binary_children, $referral_children);
                $right_binary_sponsored_children = array_intersect($right_binary_children, $referral_children);

                if (User::hasLeastChildrenWithRank($left_binary_sponsored_children, $rank->condition_sub_rank, $rank->condition_number_of_left_children) &&
                    User::hasLeastChildrenWithRank($right_binary_sponsored_children, $rank->condition_sub_rank, $rank->condition_number_of_right_children)) {
                    if($user->rank >= $rank->rank && now()->isBefore(Carbon::createFromFormat('d/m/Y',  '01/08/2022'))){
                        return $user->rank_model;
                    } else {
                        $user->rank = $rank->rank;
                        $user->save();
                    }
                    return $rank;
                }
            }
        }

        return null;

    }
}


if (!function_exists('getMLMSetting')) {

    function getSetting($key)
    {
        //Check if settings are available in cache
        if (cache()->has('mlm_settings'))
            if ($setting = collect(cache('mlm_settings'))->where('name', $key)->first())
                return $setting['value'];

        $setting = \MLM\Models\Setting::query()->where('name', $key)->first();
        if ($setting)
            return $setting->value;

        if (defined('MLM_SETTINGS') AND is_array(MLM_SETTINGS) AND array_key_exists($key, MLM_SETTINGS))
            return MLM_SETTINGS[$key]['value'];

        \Illuminate\Support\Facades\Log::error('mlmSetting => ' . $key);
        throw new Exception(trans('mlm.responses.settings.key-doesnt-exists', ['key' => $key]));
    }
}


function getEmailAndTextSetting($key)
{
    // Comment Test
    if (DB::table('email_content_settings')->exists()) {
        $setting = EmailContentSetting::query()->where('key', $key)->first();
        if ($setting && !empty($setting->body))
            return $setting->toArray();
    }

    if (isset(EMAIL_CONTENT_SETTINGS[$key]))
        return EMAIL_CONTENT_SETTINGS[$key];

    throw new Exception(trans('user.responses.main-key-settings-is-missing'));
}


if (!function_exists('chartMaker')) {
    function chartMaker($duration_type, $repo_function, $sub_function)
    {
        switch ($duration_type) {
            default:
            case "week":

                $from_day = Carbon::now()->endOfDay()->subDays(7);
                $to_day = Carbon::now();

                $processing_collection = $repo_function($from_day, $to_day);

                $result = [];
                foreach (range(-1, 5) as $day) {

                    $timestamp = Carbon::now()->startOfDay()->subDays($day)->timestamp;
                    $interval = [Carbon::now()->startOfDay()->subDays($day+1), Carbon::now()->startOfDay()->subDays($day)];


                    $result[$timestamp] = $sub_function($processing_collection, $interval);

                }
                return $result;
                break;
            case "month":
                $from_day = Carbon::now()->endOfMonth()->subMonths(12);
                $to_day = Carbon::now();

                $processing_collection = $repo_function($from_day, $to_day);
                $result = [];
                foreach (range(-1, 10) as $month) {
                    $timestamp = Carbon::now()->startOfMonth()->subMonths($month)->timestamp;
                    $interval = [Carbon::now()->startOfMonth()->subMonths($month+1), Carbon::now()->startOfMonth()->subMonths($month)];

                    $result[$timestamp] = $sub_function($processing_collection, $interval);
                }
                return $result;
                break;
            case "year":

                $from_day = Carbon::now()->endOfYear()->subYears(3);
                $to_day = Carbon::now();

                $processing_collection = $repo_function($from_day, $to_day);
                $result = [];
                foreach (range(-1, 3) as $year) {
                    $timestamp = Carbon::now()->startOfYear()->subYears($year)->timestamp;
                    $interval = [Carbon::now()->startOfYear()->subYears($year+1), Carbon::now()->startOfYear()->subYears($year)];

                    $result[$timestamp] = $sub_function($processing_collection, $interval);
                }
                return $result;
                break;
        }

    }
}
