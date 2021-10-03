<?php


namespace MLM\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MLM\Models\ResidualBonusSetting;
use MLM\tests\MLMTest;

class ResidualBonusSettingCreateFeatureTest extends MLMTest
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_create_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $resp=$this->post(route('admin.residualBonusSetting.store'), [
            'rank' => mt_rand(0,1000),
            'percentage' => mt_rand(0,1000)/10,
            'level' => mt_rand(0,1000)
        ])->assertOk();
        $this->assertDatabaseHas('residual_bonus_settings', [
            'rank' =>$resp->json()['data']['rank'],
            'percentage' =>$resp->json()['data']['percentage'],
            'level' =>  $resp->json()['data']['level']
        ]);
    }

    /**
     * @test
     */
    public function only_super_admin_create_residualBonusSetting()
    {

        $this->withHeaders($this->getHeaders(null, USER_ROLE_CLIENT));
        $this->post(route('admin.residualBonusSetting.store'), [
            'rank' =>  mt_rand(0, 100),
            'percentage' =>  mt_rand(0, 1000)/10,
            'level' =>  mt_rand(0, 100)
        ])->assertStatus(403);

    }

    /**
     * @test
     */
    public function level_is_required_for_creating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.residualBonusSetting.store'), [
            'rank' =>  mt_rand(0, 100),
            'percentage' =>  mt_rand(0, 1000)/10,
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function rank_is_required_for_creating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.residualBonusSetting.store'), [
            'level' =>  mt_rand(0, 100),
            'percentage' =>  mt_rand(0, 1000)/10,
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function percentage_is_required_for_creating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.residualBonusSetting.store'), [
            'level' =>  mt_rand(0, 100),
            'rank' =>  mt_rand(0, 100),
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function rank_and_level_should_be_unique_for_creating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        ResidualBonusSetting::factory()->create([
            'level' =>  14,
            'rank' => 3,
            'percentage' => 20.09,
        ]);
        $this->post(route('admin.residualBonusSetting.store'), [
            'level' =>  14,
            'rank' => 3,
            'percentage' => 19,
        ])->assertStatus(422);

        $this->post(route('admin.residualBonusSetting.store'), [
            'level' =>  5,
            'rank' => 3,
            'percentage' => 19,
        ])->assertStatus(200);
    }

    /**
     * @test
     */
    public function percentage_should_be_numeric_for_creating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.residualBonusSetting.store'), [
            'level' =>  mt_rand(0, 100),
            'rank' =>  mt_rand(0, 100),
            'percentage' => ' ',
        ])->assertStatus(422);

    }
    /**
    * @test
     */
    public function rank_should_be_integer_for_creating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.residualBonusSetting.store'), [
            'level' =>  mt_rand(0, 100),
            'rank' =>  mt_rand(0, 99)/100,
            'percentage' =>mt_rand(0, 1000)/10,
        ])->assertStatus(422);

    }

    /**
    * @test
     */
    public function level_should_be_integer_for_creating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.residualBonusSetting.store'), [
            'level' =>  mt_rand(0, 99)/100,
            'rank' =>  mt_rand(0, 100),
            'percentage' =>mt_rand(0, 1000)/10,
        ])->assertStatus(422);

    }
}
