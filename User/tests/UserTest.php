<?php

namespace User\tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;
use Tests\TestCase;
use User\Models\User;
use User\UserConfigure;


class UserTest extends TestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh');
        UserConfigure::seed();
        $this->app->setLocale('en');
    }

    public function hasMethod($class, $method)
    {
        $this->assertTrue(
            method_exists($class, $method),
            "$class must have method $method"
        );
    }

    public function getHeaders($id = null)
    {
        User::query()->firstOrCreate([
            'id' => '1',
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'member_id' => 1000,
            'email' => 'work@sajidjaved.com',
            'username' => 'admin',
        ]);

        $user = User::query()->when($id, function ($query) use ($id) {
            $query->where('id', $id);
        })->first();

        $user->assignRole(USER_ROLE_SUPER_ADMIN);
        $user->save();
        $hash = md5(serialize($user->getGrpcMessage()));
        return [
            'X-user-id' => $user->id,
            'X-user-hash' => $hash,
        ];
    }
}
