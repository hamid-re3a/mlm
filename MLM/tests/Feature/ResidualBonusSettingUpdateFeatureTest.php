<?php


namespace MLM\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MLM\Models\ResidualBonusSetting;
use MLM\tests\MLMTest;

class ResidualBonusSettingUpdateFeatureTest extends MLMTest
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_update_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $residualBonusSetting = ResidualBonusSetting::factory()->create();
        $resp = $this->put(route('residualBonusSetting.update'), [
            'id' => $residualBonusSetting->id,
            'rank' => mt_rand(0, 1000),
            'percentage' => mt_rand(0, 1000) / 10,
            'level' => mt_rand(0, 1000)
        ])->assertOk();
        $this->assertDatabaseHas('residual_bonus_settings', [
            'id' => $residualBonusSetting->id,
            'rank' => $resp->json()['data']['rank'],
            'percentage' => $resp->json()['data']['percentage'],
            'level' => $resp->json()['data']['level']
        ]);
    }

    /**
     * @test
     */
    public function only_super_admin_update_residualBonusSetting()
    {

        $this->withHeaders($this->getHeaders(null, USER_ROLE_CLIENT));
        $residualBonusSetting = ResidualBonusSetting::factory()->create();
        $this->put(route('residualBonusSetting.update'), [
            'id' => $residualBonusSetting->id,
            'rank' => mt_rand(0, 1000),
            'percentage' => mt_rand(0, 1000) / 10,
            'level' => mt_rand(0, 1000)
        ])->assertStatus(403);
    }

    /**
     * @test
     */
    public function level_is_required_for_updating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $residualBonusSetting = ResidualBonusSetting::factory()->create();
        $this->put(route('residualBonusSetting.update'), [
            'id' => $residualBonusSetting->id,
            'rank' => mt_rand(0, 1000),
            'percentage' => mt_rand(0, 1000) / 10,
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function rank_is_required_for_updating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $residualBonusSetting = ResidualBonusSetting::factory()->create();
        $this->put(route('residualBonusSetting.update'), [
            'id' => $residualBonusSetting->id,
            'level' => mt_rand(0, 1000),
            'percentage' => mt_rand(0, 1000) / 10,
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function percentage_is_required_for_updating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $residualBonusSetting = ResidualBonusSetting::factory()->create();
        $this->put(route('residualBonusSetting.update'), [
            'id' => $residualBonusSetting->id,
            'level' => mt_rand(0, 1000),
            'rank' => mt_rand(0, 100),
        ])->assertStatus(422);
    }
    /**
     * @test
     */

    public function id_is_required_for_updating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $this->put(route('residualBonusSetting.update'), [
            'level' => mt_rand(0, 1000),
            'rank' => mt_rand(0, 100),
            'percentage' => mt_rand(0, 1000) / 10,
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function rank_and_level_should_be_unique_for_updating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $residualBonusSetting = ResidualBonusSetting::factory()->create([
            'level' => 14,
            'rank' => 3,
            'percentage' => 20.09,
        ]);

        $this->put(route('residualBonusSetting.update'), [
            'id' => $residualBonusSetting->id + 1,
            'level' => 14,
            'rank' => 3,
            'percentage' => 5.5,
        ])->assertStatus(422);

        $this->put(route('residualBonusSetting.update'), [
            'id' => $residualBonusSetting->id,
            'level' => 14,
            'rank' => 3,
            'percentage' => 5.5,
        ])->assertStatus(200);
    }

    /**
     * @test
     */
    public function percentage_should_be_numeric_for_updating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $residualBonusSetting = ResidualBonusSetting::factory()->create();

        $this->put(route('residualBonusSetting.update'), [
            'id' => $residualBonusSetting->id,
            'level' =>$residualBonusSetting->level,
            'rank' => mt_rand(0, 100),
            'percentage' => ' ',
        ])->assertStatus(422);

    }

    /**
     * @test
     */
    public function rank_should_be_integer_for_updating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $residualBonusSetting = ResidualBonusSetting::factory()->create();

        $this->put(route('residualBonusSetting.update'), [
            'id' => $residualBonusSetting->id,
            'level' =>$residualBonusSetting->level,
            'rank' => mt_rand(0, 99)/100,
            'percentage' => mt_rand(0, 100)/10,
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function level_should_be_integer_for_creating_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $residualBonusSetting = ResidualBonusSetting::factory()->create();

        $this->put(route('residualBonusSetting.update'), [
            'id' => $residualBonusSetting->id,
            'rank' =>$residualBonusSetting->rank,
            'level' => mt_rand(0, 99)/100,
            'percentage' => mt_rand(0, 100)/10,
        ])->assertStatus(422);

    }
}
