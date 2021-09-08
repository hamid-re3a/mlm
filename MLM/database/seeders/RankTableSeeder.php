<?php

namespace MLM\database\seeders;

use Illuminate\Database\Seeder;
use MLM\Models\Rank;

/**
 * Class AuthTableSeeder.
 */
class RankTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Rank::query()->get()->count() == 0) {
            foreach (RANKS as $key => $setting) {

                Rank::query()->create([
                    'rank' => $setting['rank'],
                    'condition_converted_in_bp' => $setting['condition_converted_in_bp'],
                    'condition_sub_rank' => $setting['condition_sub_rank'],
                    'condition_direct_or_indirect' => $setting['condition_direct_or_indirect'],
                    'prize_in_pf' => $setting['prize_in_pf'],
                    'prize_alternative' => $setting['prize_alternative'],
                    'cap' => $setting['cap'],
                ]);
            }
        }
    }
}
