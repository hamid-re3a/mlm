<?php


namespace User\tests\Feature;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use MLM\Mail\UserRankChangedEmail;
use User\Models\User;
use User\tests\UserTest;

class UserFeatureTest extends UserTest
{

    /**
     * @test
     */
    public function admin_can_edit_commission_of_user()
    {
        $user = User::factory()->create();
        $this->withHeaders($this->getHeaders(1));
        $this->put(route('admin.users.toggleCommissionToBlacklist'), [
            'user_id' => $user->id,
            'deactivated_commission_type'=> TRAINER_BONUS_COMMISSION
        ])->assertOk();
        $user->refresh();
        $this->assertEquals($user->deactivated_commission_types,[TRAINER_BONUS_COMMISSION]);

    }
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
            'default_binary_position' => \MLM\Models\Tree::LEFT
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'default_binary_position' => \MLM\Models\Tree::LEFT
        ]);
    }

    /**
     * @test
     */
    public function default_binary_position_is_required_for_editing_binary_position()
    {

        $this->withHeaders($this->getHeaders());
        $this->put(route('users.binaryPosition'), [
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function default_binary_position_is_correct_enum_value_for_editing_binary_position()
    {

        $this->withHeaders($this->getHeaders());
        $this->put(route('users.binaryPosition'), [
            'default_binary_position' => '  '
        ])->assertStatus(422);

    }

    /**
     * @test
     */
    public function user_should_login_before_calling_binary_position()
    {

        $this->put(route('users.binaryPosition'), [
            'default_binary_position' => \MLM\Models\Tree::LEFT
        ])->assertStatus(401);

    }

    /**
     * @test
     */
    public function email_should_be_send_after_updating_users_rank()
    {
        Mail::fake();

        $user = User::factory()->create();
        $user->rank = $user->rank + 1;
        $user->save();
        Mail::assertSent(UserRankChangedEmail::class);

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
