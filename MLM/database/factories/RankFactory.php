<?php

namespace MLM\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MLM\Models\Rank;

class RankFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rank::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'rank' =>  $this->faker->unique(true)->numberBetween(1, 100000),
            'rank_name' => $this->faker->text(20),
            'condition_converted_in_bp' =>  mt_rand(0, 100),
            'condition_sub_rank' => mt_rand(0, 100),
            'condition_direct_or_indirect' =>  mt_rand(0, 100),
            'prize_in_pf' =>  mt_rand(0, 100),
            'cap' =>  mt_rand(0, 100),
            'prize_alternative' => $this->faker->text(20),
            'withdrawal_limit' =>  mt_rand(0, 100),
            'condition_number_of_left_children' =>  mt_rand(0, 100),
            'condition_number_of_right_children' =>  mt_rand(0, 100),

        ];
    }


}
