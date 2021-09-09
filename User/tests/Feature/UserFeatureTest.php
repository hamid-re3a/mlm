<?php


namespace User\tests\Feature;

use User\tests\UserTest;

class UserFeatureTest extends UserTest
{

    /**
     * @test
     */
    public function user_can_edit_binary_position()
    {
     $response = $this->put(route('users.binaryPosition'), [
            'id' => 1,
            'binary_position' => 'left',
        ]);
        dd($response);
        $response->assertOk();
        $response->json();
    }



}
