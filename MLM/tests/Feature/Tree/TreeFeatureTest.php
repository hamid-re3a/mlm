<?php


namespace MLM\tests\Feature\Tree;


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;
use MLM\tests\MLMTest;
use User\Models\User;

class TreeFeatureTest extends MLMTest
{
    /**
     * @test
     */
    public function tree_display_multi_level()
    {
        Mail::fake();
        $this->buildBinaryTree();
        $this->buildReferralTree();
        Artisan::call('convert:fix');

        $this->withHeaders($this->getHeaders(1, USER_ROLE_CLIENT));

        $response = $this->get(route('customer.trees.binary-multi-level').'?level=3&position=left');
        $response->assertOk();
        $response = $this->get(route('customer.trees.referral-multi-level'));
        $response->assertOk();
    }
    /**
     * @test
     */
    public function tree_display_multi_level_with_id()
    {
        Mail::fake();
        $this->buildBinaryTree();
        $this->buildReferralTree();
        $this->withHeaders($this->getHeaders(1, USER_ROLE_CLIENT));

        $response = $this->get(route('customer.trees.binary-multi-level').'?id=2&level=10');
        $response->assertOk();
        $response = $this->get(route('customer.trees.referral-multi-level').'?id=2');
        $response->assertOk();
    }

    public function buildBinaryTree($data = null)
    {
        if (is_null($data))
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
        foreach ($data as $item) {
            User::query()->firstOrCreate(['id' => $item['user_id']]);
            Tree::query()->create($item);
            Tree::fixTree();
        }
    }

    public function buildReferralTree($data = null)
    {
        if (is_null($data))
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
        foreach ($data as $item) {
            ReferralTree::query()->create($item);
            ReferralTree::fixTree();
        }
    }

}
