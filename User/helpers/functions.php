<?php

use User\Models\EmailContentSetting;
use User\Models\LoginAttemptSetting;
use User\Models\Setting;
use Illuminate\Support\Facades\DB;

function getSetting($key)
{

    if (DB::table('settings')->exists()) {
        $key_db = Setting::query()->where('key', $key)->first();
        if ($key_db && !empty($key_db->value))
            return $key_db->value;
    }

    if (isset(SETTINGS[$key]) && isset(SETTINGS[$key]['value']))
        return SETTINGS[$key]['value'];

    throw new Exception(trans('user.responses.main-key-settings-is-missing'));

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

function getLoginAttemptSetting()
{
    $intervals = [];
    $tries = [];
    if (DB::table('login_attempt_settings')->exists() && LoginAttemptSetting::query()->get()->count() > 0) {
        $intervals_db = LoginAttemptSetting::query()->orderBy('priority', 'ASC')->get();
        foreach ($intervals_db as $ri) {
            $intervals [] = $ri->blocking_duration + $ri->duration;
            $tries [] = $ri->times;
        }
        return array($intervals, $tries);
    }


    if (isset(LOGIN_ATTEMPT_SETTINGS[0])) {
        foreach (LOGIN_ATTEMPT_SETTINGS as $ri) {
            $intervals[] = $ri['blocking_duration'] + $ri['duration'];
            $tries[] = $ri['times'];
        }
        return array($intervals, $tries);
    }

    throw new Exception(trans('user.responses.main-key-settings-is-missing'));
}


function hyphenate($str, int $every = 3)
{
    return implode("-", str_split($str, $every));
}

function sumUp(array $intervals, int $key)
{
    $all_numeric = true;
    foreach ($intervals as $sub_keys) {
        if (!(is_numeric($sub_keys))) {
            $all_numeric = false;
            break;
        }
    }
    if (!$all_numeric)
        return 0;

    if ($key == 0)
        return 0;
    return $intervals[$key - 1] + sumUp($intervals, $key - 1);
}


function secondsToHumanReadable($seconds)
{
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    $time = $dtF->diff($dtT)->format('%a days, %h hours, %i minute, %s seconds ');

    return preg_replace('/(, )?(?<!\d)0 .*?(,| )/', '', $time);
}

function getDbTranslate($key,$defaultValue = null)
{

    $translate = cache()->get('dbTranslates')->where('key', $key)->first();
    if($translate)
        return $translate->value;

    \User\Models\Translate::insertOrIgnore([
        'key' => $key
    ]);

    return $defaultValue ? $defaultValue : $key;

}
