<?php

namespace User\database\factories;

use User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'last_name' => $this->faker->name,
            'username' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'rank' => 2,
            'default_binary_position' => \MLM\Models\Tree::LEFT

        ];
    }
}
