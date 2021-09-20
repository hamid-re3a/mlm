<?php


namespace MLM\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MLM\Models\Package;
use MLM\Models\PackageRoi;
use MLM\tests\MLMTest;

class PackageRoiCreateFeatureTest extends MLMTest
{
    use RefreshDatabase;


    /**
     * @test
     */
    public function user_can_create_packageRoi()
    {
        $this->withHeaders($this->getHeaders());
        $package = Package::factory()->create();
        $resp = $this->post(route('admin.packagesRoi.store'), [
            'package_id' => $package->id,
            'roi_percentage' => mt_rand(0, 1000) / 10,
            'due_date' => '2021-08-02'
        ])->assertOk();
        $this->assertDatabaseHas('package_rois', [
            'package_id' => $package->id,
            'roi_percentage' => $resp->json()['data']['roi_percentage'],
            'due_date' => '2021-08-02'
        ]);
    }

    /**
     * @test
     */
    public function only_super_admin_and_mlm_admin_can_create_packageRoi()
    {

        $this->withHeaders($this->getHeaders(null, USER_ROLE_CLIENT));

        $package = Package::factory()->create();
        $this->post(route('admin.packagesRoi.store'), [
            'package_id' => $package->id,
            'roi_percentage' => mt_rand(0, 1000) / 10,
            'due_date' => '2021-08-02'
        ])->assertStatus(403);

    }

    /**
     * @test
     */
    public function package_id_is_required_for_creating_packageRoi()
    {
        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.packagesRoi.store'), [
            'roi_percentage' => mt_rand(0, 1000) / 10,
            'due_date' => '2021-08-02'
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function roi_percentage_is_required_for_creating_packageRoi()
    {
        $package = Package::factory()->create();

        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.packagesRoi.store'), [
            'package_id' => $package->id,
            'due_date' => '2021-08-02'
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function roi_percentage_should_be_numeric_for_creating_packageRoi()
    {
        $package = Package::factory()->create();

        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.packagesRoi.store'), [
            'package_id' => $package->id,
            'due_date' => '2021-08-02',
            'roi_percentage' => 'test',
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function due_date_is_required_for_creating_packageRoi()
    {
        $package = Package::factory()->create();

        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.packagesRoi.store'), [
            'package_id' => $package->id,
            'roi_percentage' => mt_rand(0, 1000) / 10,
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function package_id_should_be_integer_for_creating_packageRoi()
    {
        $this->withHeaders($this->getHeaders());
        $this->post(route('admin.packagesRoi.store'), [
            'package_id' => ' ',
            'roi_percentage' => mt_rand(0, 1000) / 10,
            'due_date' => '2021-08-02'
        ])->assertStatus(422);
    }
    /**
     * @test
     */
    public function package_id_and_due_date_should_be_unique_for_updating_packageRoi()
    {
        $this->withHeaders($this->getHeaders());
        $packageRoi = PackageRoi::factory()->create();
        $resp = $this->post(route('admin.packagesRoi.store'), [
            'package_id' => $packageRoi->package_id,
            'due_date' => $packageRoi->due_date,
            'roi_percentage' => mt_rand(0, 1000) / 10
        ])->assertStatus(422);;

    }


}
