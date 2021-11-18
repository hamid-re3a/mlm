<?php

namespace MLM\tests\UnitTest\Order;


use Illuminate\Support\Facades\Mail;
use MLM\Models\OrderedPackage;
use MLM\Models\PackageRoi;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;
use MLM\Services\OrderResolver;
use Wallets\Services\WalletClientFacade;
use MLM\tests\MLMTest;
use Orders\Services\Grpc\Order;
use Orders\Services\Grpc\OrderPlans;
use Wallets\Services\Grpc\Deposit;

class OrderTest extends MLMTest
{
    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        ReferralTree::create(['user_id' => 1]);
        Tree::create(['user_id' => 1]);
        WalletClientFacade::shouldReceive('deposit')->andReturn( new Deposit());

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
        $this->assertTrue($bool,$msg);

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
        $user = $this->registerUser();

        //register second user
        $second_user = $this->registerUser();

        // register $third_user
        $third_user = $this->registerUser();

        // register fourth_user
        $fourth_user = $this->registerUser();

    }


    /**
     * @test
     */
    public function user_activate_binary_after_adding_new_member_to_each_side_of_binary_tree()
    {

        //register first user
        $user = $this->registerUser();

        $this->assertFalse($user->hasCompletedBinaryLegs());
        //register second user
        $second_user = $this->registerUser($user->id);

        $user->default_binary_position = "right";
        $user->save();
        // register $third_user
        $third_user = $this->registerUser($user->id);

        $this->assertTrue($user->hasCompletedBinaryLegs());
    }


    /**
     * @test
     */
    public function trainer_bonus_to_our_very_diligent_user_because_of_his_team()
    {


        $user = $this->registerUser();

        $second_user = $this->registerUser($user->id);

        $user->default_binary_position = "right";
        $user->save();

        $third_user = $this->registerUser($user->id);


        $fourth_user = $this->registerUser($second_user->id);
        $second_user->default_binary_position = "right";
        $second_user->save();
        $fifth_user = $this->registerUser($second_user->id);


        $sixth_user = $this->registerUser($third_user->id);
        $third_user->default_binary_position = "right";
        $third_user->save();
        $seventh_user = $this->registerUser($third_user->id);


        $this->assertEquals(1, $user->commissions()->type(TRAINER_BONUS_COMMISSION)->count(), 'Number of trainer bonus commissions');

        $this->assertEquals(200, $user->commissions()->type(TRAINER_BONUS_COMMISSION)->sum('amount'), 'Sum of earned commissions');

    }

    /**
     * @test
     */
    public function binary_commission_to_first_user()
    {


        $user = $this->registerUser();

        $second_user = $this->registerUser($user->id);

        $user->default_binary_position = "right";
        $user->save();

        $third_user = $this->registerUser($user->id);

        $this->assertEquals(1, $user->commissions()->type(BINARY_COMMISSION)->count(), 'Number of binary commissions');
        $this->assertEquals(7.92, $user->commissions()->type(BINARY_COMMISSION)->sum('amount'));
        $this->assertEquals($user->binaryTree->converted_points, 99);

    }
    /**
     * @test
     */
    public function binary_commission_to_first_user_prohibited()
    {


        $user = $this->registerUser();
        $user->deactivated_commission_types = [BINARY_COMMISSION];
        $user->save();

        $second_user = $this->registerUser($user->id);

        $user->default_binary_position = "right";
        $user->save();

        $third_user = $this->registerUser($user->id);

        $this->assertEquals(0, $user->commissions()->type(BINARY_COMMISSION)->count(), 'Number of binary commissions');
        $this->assertEquals(0, $user->commissions()->type(BINARY_COMMISSION)->sum('amount'));
        $this->assertEquals($user->binaryTree->converted_points, 0);

    }

    /**
     * @test
     */
    public function binary_commission_cap()
    {


        $user = $this->registerUser();

        $second_user = $this->registerUser($user->id, RANK_6_EXECUTIVE['condition_converted_in_bp'] * BF_TO_BB_RATIO);
        $user->default_binary_position = "right";
        $user->save();

        $third_user = $this->registerUser($user->id, RANK_6_EXECUTIVE['condition_converted_in_bp'] * BF_TO_BB_RATIO);

        $this->assertEquals(1, $user->commissions()->type(BINARY_COMMISSION)->count(), 'Number of binary commissions');
        $this->assertEquals(RANK_6_EXECUTIVE['cap'] * 8 / 100, $user->commissions()->type(BINARY_COMMISSION)->sum('amount'));
        $this->assertEquals((RANK_6_EXECUTIVE['condition_converted_in_bp'] * 5 - RANK_6_EXECUTIVE['cap']) * 8 / 100, $user->commissions()->type('cap-commission')->sum('amount'));
        $this->assertEquals($user->binaryTree->converted_points, RANK_6_EXECUTIVE['condition_converted_in_bp'] * BF_TO_BB_RATIO);

    }

    /**
     * @test
     */
    public function trading_profit_commission()
    {
        $user = $this->registerUser(1, 99, 1);

        $second_user = $this->registerUser($user->id);
        $user->default_binary_position = "right";
        $user->save();

        $third_user = $this->registerUser($user->id);


        PackageRoi::factory()->create([
            'package_id' => 1,
            'roi_percentage' => 3,
            'due_date' => now()->toDate()
        ]);
        $this->artisan('roi:trading')
            ->execute();

        $this->assertEquals(1, $user->commissions()->type(TRADING_PROFIT_COMMISSION)->count(), 'Number of trading commissions');
        $this->assertEquals(3 / 100 * 99, $user->commissions()->type(TRADING_PROFIT_COMMISSION)->sum('amount'));

    }

    /**
     * @test
     */
    public function trading_profit_commission_not_should_pay_to_user_prohibited()
    {
        $user = $this->registerUser(1, 99, 1);
        $user->deactivated_commission_types = [TRADING_PROFIT_COMMISSION];
        $user->save();

        $second_user = $this->registerUser($user->id);
        $user->default_binary_position = "right";
        $user->save();

        $third_user = $this->registerUser($user->id);

        PackageRoi::factory()->create([
            'package_id' => 1,
            'roi_percentage' => 3,
            'due_date' => now()->toDate()
        ]);
        $this->artisan('roi:trading')
            ->execute();

        $this->assertEquals(0, $user->commissions()->type(TRADING_PROFIT_COMMISSION)->count(), 'Number of trading commissions');
        $this->assertEquals(0, $user->commissions()->type(TRADING_PROFIT_COMMISSION)->sum('amount'));

    }
    /**
     * @test
     */
    public function no_trading_profit_commission()
    {
        $user = $this->registerUser(1, 99, 1);

        $second_user = $this->registerUser($user->id);
        $user->default_binary_position = "right";
        $user->save();

        $third_user = $this->registerUser($user->id);


        $this->artisan('roi:trading')->execute();

        $this->assertEquals(0, $user->commissions()->type(TRADING_PROFIT_COMMISSION)->count(), 'Number of trading commissions');
        $this->assertEquals(0, $user->commissions()->type(TRADING_PROFIT_COMMISSION)->sum('amount'));


    }

    /**
     * @test
     */
    public function residual_bonus_commission()
    {
        $user = $this->registerUser(1, 99, 1);

        $second_user = $this->registerUser($user->id, 5 * RANK_6_EXECUTIVE['condition_converted_in_bp'] * BF_TO_BB_RATIO);
        $user->default_binary_position = "right";
        $user->save();

        $third_user = $this->registerUser($user->id, 5 * RANK_6_EXECUTIVE['condition_converted_in_bp'] * BF_TO_BB_RATIO);


        $fourth_user = $this->registerUser($second_user->id);
        $second_user->default_binary_position = "right";
        $second_user->save();
        $fifth_user = $this->registerUser($second_user->id);


        $sixth_user = $this->registerUser($third_user->id);
        $third_user->default_binary_position = "right";
        $third_user->save();
        $seventh_user = $this->registerUser($third_user->id);

        PackageRoi::factory()->create([
            'package_id' => 1,
            'roi_percentage' => 3,
            'due_date' => now()->toDate()
        ]);
        $this->artisan('roi:trading')->execute();

        $this->assertEquals(1, $user->commissions()->type(TRADING_PROFIT_COMMISSION)->count(), 'Number of trading commissions');
        $this->assertEquals(3 / 100 * 99, $user->commissions()->type(TRADING_PROFIT_COMMISSION)->sum('amount'));


        $this->artisan('roi:residual')->execute();
        $this->assertEquals(1, $user->commissions()->type(RESIDUAL_BONUS_COMMISSION)->count(), 'Number of residual commissions');

    }

    /**
     * @test
     */
    public function direct_sell_to_father_package()
    {
        $user = $this->registerUser();

        $second_user = $this->registerUser($user->id);

        $this->assertEquals(1, $user->commissions()->type(DIRECT_SELL_COMMISSION)->count(), 'Number of direct commissions');
        $this->assertEquals(7.92, $user->commissions()->sum('amount'), 'Sum of earned commissions');
    }

    /**
     * @test
     */
    public function indirect_sell_to_father_package()
    {
        //register first user
        $user = $this->registerUser();

        //register second user
        $second_user = $this->registerUser($user->id);

        //register second user
        $third_user = $this->registerUser($second_user->id);

        $this->assertEquals(1, $user->commissions()->type(INDIRECT_SELL_COMMISSION)->count(), 'Number of direct commissions');
    }


    /**
     * @param \User\Models\User $user
     * @param int $package_price
     * @param $package_id
     * @return Order
     */
    private function createOrderWithUser(\User\Models\User $user, $package_price = 99, $package_id = 1): Order
    {


        $order_entity = OrderedPackage::factory()->create([
            'user_id' => $user->id,
            'price' => $package_price,
            'package_id' => $package_id,
            'plan' => OrderPlans::ORDER_PLAN_START
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
     * @param int $sponsor_id
     * @param int $package_price
     * @param int $package_id
     * @return \User\Models\User
     */
    private function registerUser($sponsor_id = 1, $package_price = 99, $package_id = 1): \User\Models\User
    {
        $user = \User\Models\User::factory()->create([
            'sponsor_id' => $sponsor_id
        ]);
        $order = $this->createOrderWithUser($user, $package_price, $package_id);

        list($bool, $msg) = (new OrderResolver($order))->handle();
        $this->assertTrue($bool);
        return $user;
    }


}
