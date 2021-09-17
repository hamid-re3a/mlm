<?php

namespace MLM\tests\UnitTest\Order;


use GPBMetadata\User;
use Illuminate\Support\Facades\App;
use MLM\Models\Commission;
use MLM\Models\OrderedPackage;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;
use MLM\Services\OrderResolver;
use MLM\tests\MLMTest;
use Orders\Services\Grpc\Order;
use Orders\Services\Grpc\OrderPlans;
use Orders\Services\OrderService;
use Packages\Services\Grpc\Package;

class OrderTest extends MLMTest
{
    public function setUp() : void
    {
        parent::setUp();
        ReferralTree::create(['user_id' => 1]);
        Tree::create(['user_id' => 1]);
    }

    /**
     * @test
     */
    public function add_user_to_both_tree_with_valid_ordered_package()
    {
        $user = \User\Models\User::factory()->create([
            'sponsor_id' => 1
        ]);
        $order = $this->createOrderWithUser($user);

        $this->assertNull($user->binaryTree);
        $this->assertNull($user->referralTree);

        list($bool, $msg) = (new OrderResolver($order))->handle();

        $this->assertTrue($bool);

        $user->refresh();
        $this->assertNotNull($user->binaryTree);
        $this->assertNotNull($user->referralTree);

    }

    /**
     * @param \User\Models\User $user
     * @return Order
     */
    private function createOrderWithUser(\User\Models\User $user): Order
    {


        OrderedPackage::query()->create([
            'order_id' => 1,
            'package_id' => 1,
            'user_id' => $user->id,
            'is_paid_at' => now(),
            'plan' => OrderPlans::ORDER_PLAN_START,
            'expires_at' => now()->addDays(200)
        ]);

        $order = new Order();
        $order->setId(1);
        $order->setUserId((int)$user->id);
        $order->setIsPaidAt(now()->toString());
        $order->setPlan(OrderPlans::ORDER_PLAN_START);
        $order->setPackageId((int)1);
        return $order;
    }


}
