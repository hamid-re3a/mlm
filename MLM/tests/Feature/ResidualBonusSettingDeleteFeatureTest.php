<?php


namespace MLM\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MLM\Models\ResidualBonusSetting;
use MLM\tests\MLMTest;

class ResidualBonusSettingDeleteFeatureTest extends MLMTest
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_delete_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
        $residualBonusSetting = ResidualBonusSetting::factory()->create();
        $this->delete(route('residualBonusSetting.destroy'), [
            'id' => $residualBonusSetting->id,
        ])->assertOk();
        $this->assertDatabaseMissing('residual_bonus_settings', [
            'id' => $residualBonusSetting->id,
        ]);

    }

    /**
     * @test
     */
    public function only_super_admin_can_delete_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders(null, USER_ROLE_CLIENT));
        $residualBonusSetting = ResidualBonusSetting::factory()->create();
        $this->delete(route('residualBonusSetting.destroy'), [
            'id' => $residualBonusSetting->id,
        ])->assertStatus(403);

    }

    /**
     * @test
     */
    public function id_is_required_for_deleting_residualBonusSetting()
    {
        $this->withHeaders($this->getHeaders());
         ResidualBonusSetting::factory()->create();
        $this->delete(route('residualBonusSetting.destroy'), [
        ])->assertStatus(422);

    }

}
