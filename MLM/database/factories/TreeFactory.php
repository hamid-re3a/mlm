<?php

namespace MLM\database\factories;

use MLM\Models\Tree;
use Illuminate\Database\Eloquent\Factories\Factory;
use User\Models\User;

class TreeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tree::class;

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
