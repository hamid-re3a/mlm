<?php

namespace MLM\database\seeders;

use Illuminate\Database\Seeder;
use MLM\Models\Setting;

/**
 * Class AuthTableSeeder.
 */
class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        if(defined('MLM_SETTINGS') AND is_array(MLM_SETTINGS)) {
            $settings = [];
            $now = now()->toDateTimeString();
            foreach(MLM_SETTINGS AS $key => $setting)
                $settings[] = [
                    'name' => $key,
                    'value' => $setting['value'],
                    'title' => $setting['title'],
                    'description' => $setting['description'],
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            Setting::query()->upsert($settings,'name');
            cache(['mlm_settings' =>  $settings]);
        }


    }
}
