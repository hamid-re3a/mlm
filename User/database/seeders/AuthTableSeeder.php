<?php

namespace User\database\seeders;

use App\Jobs\User\UserDataJob;
use User\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Class AuthTableSeeder.
 */
class AuthTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (USER_ROLES as $role) {
            Role::query()->firstOrCreate(['name' => $role]);
        }

    }
}
