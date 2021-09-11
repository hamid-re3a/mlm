<?php
namespace MLM\database\seeders;

use Illuminate\Database\Seeder;
use MLM\Models\Package;

class PackageTableSeeder extends Seeder
{
    public function run()
    {
        if (Package::query()->count() > 0)
            return;
        if(is_null(config('mlm.packages')))
            throw new \Exception('packages.config-key-setting-missing');

        foreach (config('mlm.packages') as $key => $setting) {
            if (!Package::query()->whereName($setting['name'])->exists()) {
                Package::query()->create([
                    'name' => $setting['name'],
                    'short_name' => $setting['short_name'],
                ]);
            }
        }

    }

}
