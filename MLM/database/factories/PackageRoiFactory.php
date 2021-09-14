<?php

namespace MLM\database\factories;

use Carbon\Carbon;
use MLM\Models\Package;
use MLM\Models\PackageRoi;
use Illuminate\Database\Eloquent\Factories\Factory;
use User\Models\User;

class PackageRoiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PackageRoi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $package = Package::factory()->create();
        $user = User::factory()->create();
        return [
            'package_id' => $package->id,
            'due_date' => $this->faker->date('Y-m-d','now'),
            'user_id' =>  $user->id,
            'roi_percentage' => (mt_rand(0, 1000) / 10),
        ];
    }
}
