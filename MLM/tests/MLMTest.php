<?php


namespace MLM\tests;


use Illuminate\Foundation\Testing\RefreshDatabase;
use MLM\MLMConfigure;
use MLM\Models\Tree;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;
use Tests\TestCase;
use User\Models\User;
use User\UserConfigure;

class MLMTest extends TestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh');
        UserConfigure::seed();
        MLMConfigure::seed();

        $this->app->setLocale('en');
    }

    public function hasMethod($class, $method)
    {
        $this->assertTrue(
            method_exists($class, $method),
            "$class must have method $method"
        );
    }

    public function buildBinaryTree($data = null)
    {
        if (is_null($data))
            $data = [
                ['id' => 1, 'position' => null, 'parent_id' => null],
                ['id' => 2, 'position' => 'left', 'parent_id' => 1],
                ['id' => 3, 'position' => 'right', 'parent_id' => 1],
                ['id' => 4, 'position' => 'right', 'parent_id' => 2],
                ['id' => 5, 'position' => 'left', 'parent_id' => 2],
                ['id' => 6, 'position' => 'right', 'parent_id' => 3],
                ['id' => 7, 'position' => 'left', 'parent_id' => 3],
                ['id' => 8, 'position' => 'right', 'parent_id' => 4],
                ['id' => 9, 'position' => 'left', 'parent_id' => 4],
                ['id' => 10, 'position' => 'right', 'parent_id' => 5],
                ['id' => 11, 'position' => 'left', 'parent_id' => 5],
            ];
        foreach ($data as $item) {
            Tree::factory()->create($item);
            Tree::fixTree();
        }
    }

    public function getHeaders($id = null, $role = null)
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

        $user->assignRole($role ? $role : USER_ROLE_SUPER_ADMIN);
        $user->save();
        $hash = md5(serialize($user->getUserService()));
        return [
            'X-user-id' => $user->id,
            'X-user-hash' => $hash,
        ];
    }
}
