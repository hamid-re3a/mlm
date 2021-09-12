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
            'email' => $this->faker->safeEmail(),
            'username' => $this->faker->userName,
            'sponsor_id' => User::query()->inRandomOrder()->first()->username,
            'member_id' => mt_rand(121212121,999999999)
        ];
    }
}
