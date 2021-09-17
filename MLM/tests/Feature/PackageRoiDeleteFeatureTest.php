<?php


namespace MLM\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MLM\Models\Package;
use MLM\Models\PackageRoi;
use MLM\tests\MLMTest;

class PackageRoiDeleteFeatureTest extends MLMTest
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_delete_packageRoi()
    {
        $this->withHeaders($this->getHeaders());
        $packageRoi = PackageRoi::factory()->create();
        $this->delete(route('packagesRoi.destroy'), [
            'package_id' => $packageRoi->package_id,
            'due_date' => $packageRoi->due_date,
        ])->assertOk();
        $this->assertDatabaseMissing('package_rois', [
            'package_id' => $packageRoi->package_id,
            'due_date' => $packageRoi->due_date,
        ]);

    }

    /**
     * @test
     */
    public function only_super_admin_and_mlm_admin_can_delete_packageRois()
    {
        $this->withHeaders($this->getHeaders(null, USER_ROLE_CLIENT));
        $packageRoi = PackageRoi::factory()->create();
        $this->delete(route('packagesRoi.destroy'), [
            'package_id' => $packageRoi->package_id,
            'due_date' => $packageRoi->due_date,
        ])->assertStatus(403);

    }

    /**
     * @test
     */
    public function package_id_is_required_for_deleting_packageRoi()
    {
        $this->withHeaders($this->getHeaders());
        $this->delete(route('packagesRoi.destroy'), [
            'due_date' =>'2021-08-02'
        ])->assertStatus(422);

    }    /**
     * @test
     */
    public function due_date_is_required_for_deleting_packageRoi()
    {
        $this->withHeaders($this->getHeaders());
        $packageRoi = PackageRoi::factory()->create();
        $this->delete(route('packagesRoi.destroy'), [
            'package_id' => $packageRoi->package_id,
        ])->assertStatus(422);

    }


}
