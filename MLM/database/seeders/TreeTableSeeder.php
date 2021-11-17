<?php

namespace MLM\database\seeders;

use Illuminate\Database\Seeder;
use MLM\Models\OrderedPackage;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;
use Orders\Services\Grpc\OrderPlans;
use User\Models\User;
use User\Services\UserService;

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
        if (app()->environment() != 'testing')
            if (ReferralTree::query()->get()->count() == 0) {
                $this->buildBinaryTree();
                $this->buildReferralTree();
            }

    }


    public function buildBinaryTree($data = null)
    {
        if (is_null($data))
            if (!in_array(app()->environment(), ['production', 'staging'])) {

                $data = [
                    ['id' => 1, 'user_id' => 1, 'position' => null, 'parent_id' => null],
                    ['id' => 2, 'user_id' => 2, 'position' => 'left', 'parent_id' => 1],
                    ['id' => 3, 'user_id' => 3, 'position' => 'right', 'parent_id' => 1],
                    ['id' => 4, 'user_id' => 4, 'position' => 'right', 'parent_id' => 2],
                    ['id' => 5, 'user_id' => 5, 'position' => 'left', 'parent_id' => 2],
                    ['id' => 6, 'user_id' => 6, 'position' => 'right', 'parent_id' => 3],
                    ['id' => 7, 'user_id' => 7, 'position' => 'left', 'parent_id' => 3],
                    ['id' => 8, 'user_id' => 8, 'position' => 'right', 'parent_id' => 4],
                    ['id' => 9, 'user_id' => 9, 'position' => 'left', 'parent_id' => 4],
                    ['id' => 10, 'user_id' => 10, 'position' => 'right', 'parent_id' => 5],
                    ['id' => 11, 'user_id' => 11, 'position' => 'left', 'parent_id' => 5],
                    ['id' => 12, 'user_id' => 12, 'position' => 'left', 'parent_id' => 6],
                    ['id' => 13, 'user_id' => 13, 'position' => 'right', 'parent_id' => 6],
                    ['id' => 14, 'user_id' => 14, 'position' => 'right', 'parent_id' => 7],
                    ['id' => 15, 'user_id' => 15, 'position' => 'left', 'parent_id' => 7],
                    ['id' => 16, 'user_id' => 16, 'position' => 'right', 'parent_id' => 8],
                    ['id' => 17, 'user_id' => 17, 'position' => 'left', 'parent_id' => 9],
                    ['id' => 18, 'user_id' => 18, 'position' => 'right', 'parent_id' => 10],
                    ['id' => 19, 'user_id' => 19, 'position' => 'left', 'parent_id' => 11],
                    ['id' => 20, 'user_id' => 20, 'position' => 'right', 'parent_id' => 12],
                    ['id' => 21, 'user_id' => 21, 'position' => 'left', 'parent_id' => 13],
                    ['id' => 22, 'user_id' => 22, 'position' => 'right', 'parent_id' => 13],
                ];
            } else {
                $data = [
                    ['id' => 1, 'user_id' => 1, 'position' => null, 'parent_id' => null],
                ];
            }
        foreach ($data as $item) {
            /** @var  $_user User */
            $_user = app(UserService::class)->findByIdOrFail($item['user_id']);
            $_user->rank = $item['user_id'] % 14;
            $_user->saveQuietly();
            OrderedPackage::query()->create([

                'order_id' => $item['user_id'],
                'price' => 99,

                'direct_percentage' => 8,
                'binary_percentage' => 8,

                'user_id' => $item['user_id'],
                'is_paid_at' => now()->toDateString(),
                'is_resolved_at' => now()->toDateString(),
                'is_commission_resolved_at' => now()->toDateString(),

                'validity_in_days' => 200,
                'expires_at' => now()->addDays(200)->toDateString(),
                'package_id' => 1,
                'plan' => OrderPlans::ORDER_PLAN_START
            ]);
            Tree::query()->create($item);
            Tree::fixTree();
        }
    }

    public function buildReferralTree($data = null)
    {
        if (is_null($data))
            if (!in_array(app()->environment(), ['production', 'staging'])) {

                $data = [
                    ['id' => 1, 'user_id' => 1, 'parent_id' => null],
                    ['id' => 2, 'user_id' => 2, 'parent_id' => 1],
                    ['id' => 3, 'user_id' => 3, 'parent_id' => 1],
                    ['id' => 4, 'user_id' => 4, 'parent_id' => 1],
                    ['id' => 5, 'user_id' => 5, 'parent_id' => 1],
                    ['id' => 6, 'user_id' => 6, 'parent_id' => 1],
                    ['id' => 7, 'user_id' => 7, 'parent_id' => 1],
                    ['id' => 8, 'user_id' => 8, 'parent_id' => 1],
                    ['id' => 9, 'user_id' => 9, 'parent_id' => 1],
                    ['id' => 10, 'user_id' => 10, 'parent_id' => 1],
                    ['id' => 11, 'user_id' => 11, 'parent_id' => 1],
                    ['id' => 12, 'user_id' => 12, 'parent_id' => 1],
                    ['id' => 13, 'user_id' => 13, 'parent_id' => 2],
                    ['id' => 14, 'user_id' => 14, 'parent_id' => 2],
                    ['id' => 15, 'user_id' => 15, 'parent_id' => 2],
                    ['id' => 16, 'user_id' => 16, 'parent_id' => 2],
                    ['id' => 17, 'user_id' => 17, 'parent_id' => 2],
                    ['id' => 18, 'user_id' => 18, 'parent_id' => 12],
                    ['id' => 19, 'user_id' => 19, 'parent_id' => 12],
                    ['id' => 20, 'user_id' => 20, 'parent_id' => 12],
                    ['id' => 21, 'user_id' => 21, 'parent_id' => 12],
                    ['id' => 22, 'user_id' => 22, 'parent_id' => 12],
                ];
            } else {
                $data = [
                    ['id' => 1, 'user_id' => 1, 'parent_id' => null],
                ];
            }
        foreach ($data as $item) {
            ReferralTree::query()->create($item);
            ReferralTree::fixTree();
        }
    }
}
