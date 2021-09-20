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
        $user = User::query()->inRandomOrder()->first();
        $days=1;
        //in case duplication on mix of package_id and due_date , the date should be changed
        $dueDate=PackageRoi::whereDueDate($this->date($days))->wherePackageId($package->id)->count()>0?$this->date($days+1):$this->date($days);
        return [
            'package_id' => $package->id,
            'due_date' => $dueDate,
            'user_id' =>  $user->id,
            'roi_percentage' => (mt_rand(0, 1000) / 10),
        ];
    }

    private function date($days)
    {
        return $this->faker->dateTimeInInterval($startDate = '-'.$days.' days', $interval = '-'.$days.' days', $timezone = null)->format('Y-m-d');

    }
}
