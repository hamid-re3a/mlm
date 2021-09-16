<?php


namespace MLM\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MLM\Models\Package;
use MLM\Models\PackageRoi;
use MLM\tests\MLMTest;

class PackageRoiFetchFeatureTest extends MLMTest
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_get_packageRois()
    {
        $this->withHeaders($this->getHeaders());
        $package = Package::factory()->create();
        $packageRoi = PackageRoi::factory()->create([
            'package_id' => $package->id,
            'roi_percentage' => mt_rand(0, 1000) / 10,
            'due_date' => '2021-08-02'
        ]);
        $packagesRois = PackageRoi::factory()->count(3)->create();
        $response = $this->get(route('packagesRoi.index'))
            ->assertOk();
        $response->assertJsonStructure(
            [
                "status",
                "message",
                "data",
            ]
        );
        $response->assertJsonCount(count($packagesRois) + 1, 'data');
        $response->assertJsonFragment([
            'package_id' => $package->id,
            'due_date' => '2021-08-02'
        ]);

    }

    /**
     * @test
     */
    public function only_super_admin_and_mlm_admin_can_get_packageRois()
    {
        $this->withHeaders($this->getHeaders(null, USER_ROLE_CLIENT));
        PackageRoi::factory()->count(3)->create();
        $this->get(route('packagesRoi.index'))
            ->assertStatus(403);


    }

    /**
     * @test
     */
    public function user_can_get_packageRoi_by_package_id_due_date()
    {
        $this->withHeaders($this->getHeaders());
        $package = Package::factory()->create();
        $packageRoi = PackageRoi::factory()->create([
            'package_id' => $package->id,
            'roi_percentage' => mt_rand(0, 1000) / 10,
            'due_date' => '2021-08-02'
        ]);
        $response = $this->get(route('packagesRoi.show', [
            'package_id' => $package->id,
            'due_date' => '2021-08-02'
        ]))->assertOk();
        $response->assertJsonStructure(
            [
                "status",
                "message",
                "data",
            ]
        );
        $response->assertJsonFragment([
            'package_id' => $package->id,
            'due_date' => '2021-08-02'
        ]);

    }

    /**
     * @test
     */
    public function only_super_admin_and_mlm_admin_can_get_packageRoi_by_package_id_due_date()
    {
        $this->withHeaders($this->getHeaders(null, USER_ROLE_CLIENT));
        PackageRoi::factory()->create();
        $this->get(route('packagesRoi.show'))
            ->assertStatus(403);


    }


}
