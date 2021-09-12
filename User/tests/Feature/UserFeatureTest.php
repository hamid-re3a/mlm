<?php


namespace User\tests\Feature;

use User\Models\User;
use User\tests\UserTest;

class UserFeatureTest extends UserTest
{

    /**
     * @test
     */
    public function user_can_edit_binary_position()
    {
        $user = User::factory()->create([
            'default_binary_position' => \MLM\Models\Tree::RIGHT
        ]);
        $this->withHeaders($this->getHeaders($user->id));
        $this->put(route('users.binaryPosition'), [
            'id' => $user->id,
            'default_binary_position' => \MLM\Models\Tree::LEFT
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'default_binary_position' => \MLM\Models\Tree::LEFT

        ]);
    }

    /**
     * @test
     */
    public function user_id_is_required_for_editing_binary_position()
    {
        $this->put(route('users.binaryPosition'), [
            'id' => 1,
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function default_binary_position_is_required_for_editing_binary_position()
    {
        $this->put(route('users.binaryPosition'), [
            'default_binary_position' => \MLM\Models\Tree::RIGHT
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function default_binary_position_is_correct_enum_value_for_editing_binary_position()
    {
        $this->put(route('users.binaryPosition'), [
            'id' => 1,
            'default_binary_position' => '  '
        ])->assertStatus(422);

    }

    /**
     * @test
     */
    public function user_can_edit_only_her_own_binary_position()
    {
        $user = User::factory()->create();
        $resp = $this->put(route('users.binaryPosition'), [
            'id' => $user->id,
            'default_binary_position' => \MLM\Models\Tree::LEFT
        ])->assertStatus(400);

    }



//    /**
//     * @test
//     */
//    public function only_super_admin_can_edit_binary_position()
//    {
//        $user = User::factory()->create();
//        $user->assignRole(USER_ROLE_CLIENT);
//        $user->save();
//        $hash = md5(serialize($user->getUserService()));
//        $this->put(route('users.binaryPosition'), [
//            'id' => $user->id,
//            'default_binary_position' => \MLM\Models\Tree::RIGHT
//        ], [
//            'X-user-id' => $user->id,
//            'X-user-hash' => $hash,
//        ])->assertStatus(403);
//
//    }


}
