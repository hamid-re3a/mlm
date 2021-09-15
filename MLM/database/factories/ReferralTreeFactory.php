<?php

namespace MLM\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MLM\Models\ReferralTree;
use User\Models\User;

class ReferralTreeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReferralTree::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id
        ];
    }
}
