<?php

namespace User\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use User\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'username' => $this->faker->userName,
            'sponsor_id' => User::query()->inRandomOrder()->first()->id,
            'member_id' => mt_rand(121212121,999999999),
            'rank' => 0,
            'default_binary_position' => \MLM\Models\Tree::LEFT
        ];
    }
}
