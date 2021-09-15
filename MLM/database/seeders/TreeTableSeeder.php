<?php

namespace MLM\database\seeders;

use Illuminate\Database\Seeder;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;

/**
 * Class AuthTableSeeder.
 */
class TreeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(app()->environment() != 'testing')
        if (ReferralTree::query()->get()->count() == 0) {
            ReferralTree::create(['user_id' => 1]);
            Tree::create(['user_id' => 1]);
        }
    }
}
