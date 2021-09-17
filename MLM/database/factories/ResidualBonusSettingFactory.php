<?php

namespace MLM\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MLM\Models\ResidualBonusSetting;

class ResidualBonusSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ResidualBonusSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "rank" =>  $this->faker->unique(true)->numberBetween(1, 16),
            "level" =>$this->faker->unique(true)->numberBetween(1, 9),
            "percentage" => mt_rand(0,1000)/10,
        ];
    }
}
