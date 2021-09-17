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
use MLM\Services\OrderedPackageService;
use Packages\Services\Grpc\Package;

class OrderTest extends MLMTest
{
    public function setUp(): void
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
     * @test
     */
    public function add_four_user_to_same_sponsor_cause_no_problem()
    {

        //register first user
        list($user, $bool) = $this->registerUser();

        //register second user
        list($second_user, $bool) = $this->registerUser();

        // register $third_user
        list($third_user, $bool) = $this->registerUser();

        // register fourth_user
        list($fourth_user, $bool) = $this->registerUser();

    }


    /**
     * @test
     */
    public function user_activate_binary_after_adding_new_member_to_each_side_of_binary_tree()
    {

        //register first user
        /** @var $user \User\Models\User */
        list($user, $bool) = $this->registerUser();

        $this->assertFalse($user->hasCompletedBinaryLegs());
        //register second user
        list($second_user, $bool) = $this->registerUser($user->id);

        $user->default_binary_position = "right";
        $user->save();
        // register $third_user
        list($third_user, $bool) = $this->registerUser($user->id);


        $this->assertTrue($user->hasCompletedBinaryLegs());

    }

    /**
     * @test
     */
    public function direct_sell_to_father_package()
    {
        list($user, $bool) = $this->registerUser();

        list($second_user, $bool) = $this->registerUser($user->id);

        $this->assertEquals(1, $user->commissions()->type(DIRECT_SELL_COMMISSION)->count(), 'Number of direct commissions');
        $this->assertEquals(7.92, $user->commissions()->sum('amount'), 'Sum of earned commissions');
    }

    /**
     * @test
     */
    public function indirect_sell_to_father_package()
    {
        //register first user
        list($user, $bool) = $this->registerUser();

        //register second user
        list($second_user, $bool) = $this->registerUser($user->id);

        //register second user
        list($third_user, $bool) = $this->registerUser($second_user->id);

        $this->assertEquals(1, $user->commissions()->type(INDIRECT_SELL_COMMISSION)->count(), 'Number of direct commissions');
    }


    /**
     * @param \User\Models\User $user
     * @return Order
     */
    private function createOrderWithUser(\User\Models\User $user): Order
    {


        $order_entity = OrderedPackage::factory()->create([
            'user_id' => $user->id,
        ]);

        $order_entity->packageIndirectCommission()->create([
            'level' => '1',
            'percentage' => '3'
        ]);
        $order = new Order();
        $order->setId($order_entity->order_id);
        $order->setUserId((int)$user->id);
        $order->setIsPaidAt(now()->toString());
        $order->setPlan(OrderPlans::ORDER_PLAN_START);
        $order->setPackageId((int)$order_entity->package_id);
        return $order;
    }

    /**
     * @param int $id
     * @return array
     */
    private function registerUser($id = 1): array
    {
        $user = \User\Models\User::factory()->create([
            'sponsor_id' => $id
        ]);
        $order = $this->createOrderWithUser($user);

        list($bool, $msg) = (new OrderResolver($order))->handle();
        $this->assertTrue($bool);
        return array($user, $bool);
    }


}
