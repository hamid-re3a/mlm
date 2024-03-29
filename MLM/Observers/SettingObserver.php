<?php


namespace MLM\Observers;

use MLM\Models\Setting;

class SettingObserver
{
    public function updating(Setting $setting)
    {
        $data = array_merge($setting->getOriginal(),[
            'actor_id' => auth()->user()->id,
            'setting_id' => $setting->id,
        ]);
        unset($data['id']);
        $setting->histories()->create($data);

    }
}
