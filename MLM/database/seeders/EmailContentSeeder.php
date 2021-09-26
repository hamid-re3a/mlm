<?php


namespace MLM\database\seeders;

use Illuminate\Database\Seeder;
use MLM\Models\EmailContentSetting;

/**
 * Class AuthTableSeeder.
 */
class EmailContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {

        foreach (EMAIL_CONTENT_SETTINGS as $key => $setting) {

            if (!EmailContentSetting::query()->whereKey($key)->exists()) {
                EmailContentSetting::query()->create([
                    'key' => $key,
                    'is_active' => $setting['is_active'],
                    'subject' => $setting['subject'],
                    'from' => $setting['from'],
                    'from_name' => $setting['from_name'],
                    'body' => $setting['body'],
                    'variables' => $setting['variables'],
                    'variables_description' => $setting['variables_description'],
                    'type' => $setting['type'],
                ]);
            }
        }

    }
}
