<?php

namespace MLM\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MLM\Models\OrderedPackage;
use MLM\Models\Package;
use Orders\Services\Grpc\OrderPlans;
use User\Models\User;

class OrderedPackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderedPackage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $package_id = $this->faker->numberBetween(1, 16);
        return [
            'order_id' => $this->faker->unique(true)->numberBetween(1, 1000000),
            'package_id' => $package_id,
            'price' => 99,
            'user_id' => User::factory()->create()->id,
            'is_paid_at' => now(),
            'plan' => OrderPlans::ORDER_PLAN_START,
            'expires_at' => now()->addDays(200),



            'direct_percentage' => 8,
            'binary_percentage' => 8,

        ];
    }
}
