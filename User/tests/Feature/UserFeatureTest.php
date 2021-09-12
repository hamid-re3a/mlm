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
        $user=User::factory()->create([
            'default_binary_position'=>\MLM\Models\Tree::RIGHT
        ]);
        $response = $this->put(route('users.binaryPosition'), [
            'id' => $user->id,
            'default_binary_position' => $user->default_binary_position,
        ]);
        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'id'=>$user->id,
            'default_binary_position' =>$user->default_binary_position,

        ]);
    }

    /**
     * @test
     */
    public function default_binary_position_is_required_for_editing_binary_position()
    {

        $this->put(route('users.binaryPosition'), [
            'id' => 1,
        ])->assertStatus(422);
    }

    public function user_id_required_for_editing_binary_position0()
    {
        $this->put(route('users.binaryPosition'), [
            'default_binary_position' =>\MLM\Models\Tree::RIGHT
        ])->assertStatus(422);
    }


}
