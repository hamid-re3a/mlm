<?php


namespace MLM\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MLM\Http\Resources\Rank\RankResource;
use MLM\Models\Rank;
use MLM\Models\ResidualBonusSetting;
use MLM\tests\MLMTest;

class ResidualBonusSettingFetchDataFeatureTest extends MLMTest
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_get_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $rank=Rank::factory()->create();
        $residualBonusSetting = ResidualBonusSetting::factory()->create([
            'rank' => $rank->rank,
        ]);
        $response = $this->get(route('admin.residualBonusSetting.index'))->assertOk();
        $response->assertJsonStructure(
            [
                "status",
                "message",
                "data",
            ]
        );
      

    }

    /**
     * @test
     */
    public function only_super_admin_can_get_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders(null, USER_ROLE_CLIENT));
        ResidualBonusSetting::factory()->create();
        $this->get(route('admin.residualBonusSetting.index'))->assertStatus(403);

    }

    /**
     * @test
     */
    public function user_can_get_residualBonusSetting_by_id()
    {
        $this->withHeaders($this->getHeaders());
        $rank=Rank::factory()->create();
        $residualBonusSetting = ResidualBonusSetting::factory()->create([
            'rank' => $rank->rank,
        ]);
        $response = $this->get(route('admin.residualBonusSetting.show', [
            'id' => $residualBonusSetting->id,
        ]))->assertOk();
        $response->assertJsonStructure(
            [
                "status",
                "message",
                "data",
            ]
        );
        $response->assertJsonFragment([
            'id' => $residualBonusSetting->id,
            'percentage' => "$residualBonusSetting->percentage",
            'level' => "$residualBonusSetting->level",
            'rank' => "$rank->rank",
            'rank_name' => $rank->rank_name,
            'condition_converted_in_bp' => "$rank->condition_converted_in_bp",
            'condition_sub_rank' => "$rank->condition_sub_rank",
            'condition_direct_or_indirect' => "$rank->condition_direct_or_indirect",

        ]);

    }

    /**
     * @test
     */
    public function only_super_admin_can_get_residualBonusSetting_by_id()
    {
        $this->withHeaders($this->getHeaders(null, USER_ROLE_CLIENT));
        $residualBonusSetting = ResidualBonusSetting::factory()->create();
        $this->get(route('admin.residualBonusSetting.show', [
            'id' => $residualBonusSetting->id,
        ]))->assertStatus(403);

    }
}
