<?php


namespace User\tests\Unit;

use User\Models\User;
use User\tests\UserTest;

class UserUnitTest extends UserTest
{

    /**
     * @test
     */
    public function user_can_should_have_sponsor()
    {
        $user = User::factory()->create();
        $this->assertNotNull($user->sponsor);
    }


}
