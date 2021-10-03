<?php

namespace MLM\database\seeders;

use Illuminate\Database\Seeder;
use MLM\Models\Rank;
use MLM\Models\ResidualBonusSetting;

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

                $rank = Rank::query()->create([
                    'rank' => $setting['rank'],
                    'rank_name' => $setting['rank_name'],
                    'condition_converted_in_bp' => $setting['condition_converted_in_bp'],
                    'condition_sub_rank' => $setting['condition_sub_rank'],
                    'condition_direct_or_indirect' => $setting['condition_direct_or_indirect'],
                    'prize_in_pf' => $setting['prize_in_pf'],
                    'prize_alternative' => $setting['prize_alternative'],
                    'cap' => $setting['cap'],
                    'withdrawal_limit' => $setting['withdrawal_limit'],
                    'condition_number_of_left_children' => $setting['condition_number_of_left_children'],
                    'condition_number_of_right_children' => $setting['condition_number_of_right_children'],
                ]);
                if (isset($setting['residual_bonus_settings'])) {
                    foreach ($setting['residual_bonus_settings'] as $roi_commission) {
                        $rank->residualBonusSettings()->create([
                            'level' => $roi_commission['level'],
                            'percentage' => $roi_commission['percentage'],
                        ]);
                    }
                }
            }
        }
    }
}
