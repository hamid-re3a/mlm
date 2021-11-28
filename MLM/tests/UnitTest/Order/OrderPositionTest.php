<?php

namespace MLM\tests\UnitTest\Order;


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use MLM\Models\OrderedPackage;
use MLM\Models\PackageRoi;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;
use MLM\Services\OrderResolver;
use User\Models\User;
use Wallets\Services\Grpc\WalletClientFacade;
use MLM\tests\MLMTest;
use Orders\Services\Grpc\Order;
use Orders\Services\Grpc\OrderPlans;
use Wallets\Services\Grpc\Deposit;

class OrderPositionTest extends MLMTest
{
    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /**
     * @test
     */
    public function add_user_to_tree_with_position()
    {
        $this->buildBinaryTree2();
        $this->buildReferralTree2();

        $user = User::query()->find(1);
        $user->removeRole(USER_ROLE_SUPER_ADMIN);
        Artisan::call('convert:fix');
        $user = \User\Models\User::factory()->create([
            'sponsor_id' => 1
        ]);
        $order_entity = OrderedPackage::factory()->create([
            'user_id' => $user->id,
            'price' => 99,
            'package_id' => 1,
            'attach_user_position' => 0,
            'attach_user_id' => 19,
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
        $order->setAttachUserId((int)$order_entity->attach_user_id);
        $order->setAttachUserPosition((int)$order_entity->attach_user_position);



        $this->assertNull($user->binaryTree);

        list($bool, $msg) = (new OrderResolver($order))->handle();
        $this->assertTrue($bool,$msg);

        $user->refresh();
        $this->assertNotNull($user->binaryTree);
        $this->assertNotNull($user->referralTree);

    }

    /**
     * @test
     */
    public function add_user_to_tree_with_position2()
    {
        $this->buildBinaryTree2();
        $this->buildReferralTree2();

        $user = User::query()->find(1);
        $user->removeRole(USER_ROLE_SUPER_ADMIN);
        Artisan::call('convert:fix');
        $user = \User\Models\User::factory()->create([
            'sponsor_id' => 1
        ]);
        $order_entity = OrderedPackage::factory()->create([
            'user_id' => $user->id,
            'price' => 99,
            'package_id' => 1,
            'attach_user_position' => 1,
            'attach_user_id' => 19,
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
        $order->setAttachUserId((int)$order_entity->attach_user_id);
        $order->setAttachUserPosition((int)$order_entity->attach_user_position);



        $this->assertNull($user->binaryTree);

        list($bool, $msg) = (new OrderResolver($order))->handle();
        $this->assertFalse($bool,$msg);

        $user->refresh();
        $this->assertNull($user->binaryTree);

    }

    /**
     * @test
     */
    public function add_user_to_tree_with_right_correct_position()
    {
        $this->buildBinaryTree2();
        $this->buildReferralTree2();

        $user = User::query()->find(1);
        $user->removeRole(USER_ROLE_SUPER_ADMIN);
        Artisan::call('convert:fix');
        $user = \User\Models\User::factory()->create([
            'sponsor_id' => 2
        ]);
        $order_entity = OrderedPackage::factory()->create([
            'user_id' => $user->id,
            'price' => 99,
            'package_id' => 1,
            'attach_user_position' => 1,
            'attach_user_id' => 16,
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
        $order->setAttachUserId((int)$order_entity->attach_user_id);
        $order->setAttachUserPosition((int)$order_entity->attach_user_position);


        $this->assertNull($user->binaryTree);

        list($bool, $msg) = (new OrderResolver($order))->handle();
        $this->assertTrue($bool,$msg);

        $user->refresh();
        $this->assertNotNull($user->binaryTree);
        $this->assertNotNull($user->referralTree);

    }


    /**
     * @test
     */
    public function admin_can_insert_every_where()
    {
        $this->buildBinaryTree2();
        $this->buildReferralTree2();

        Artisan::call('convert:fix');
        $user = User::query()->find(1);
        $user->assignRole(USER_ROLE_SUPER_ADMIN);
        $user = \User\Models\User::factory()->create([
            'sponsor_id' => 1
        ]);
        $order_entity = OrderedPackage::factory()->create([
            'user_id' => $user->id,
            'price' => 99,
            'package_id' => 1,
            'attach_user_position' => 0,
            'attach_user_id' => 10,
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
        $order->setAttachUserId((int)$order_entity->attach_user_id);
        $order->setAttachUserPosition((int)$order_entity->attach_user_position);


        $this->assertNull($user->binaryTree);

        list($bool, $msg) = (new OrderResolver($order))->handle();
        $this->assertTrue($bool,$msg);

        $user->refresh();
        $this->assertNotNull($user->binaryTree);
        $this->assertNotNull($user->referralTree);


    }


    /**
     * @test
     */
    public function admin_can_not_add_occupied_position()
    {
        $this->buildBinaryTree2();
        $this->buildReferralTree2();

        Artisan::call('convert:fix');
        $user = User::query()->find(1);
        $user->assignRole(USER_ROLE_SUPER_ADMIN);
        $user = \User\Models\User::factory()->create([
            'sponsor_id' => 1
        ]);
        $order_entity = OrderedPackage::factory()->create([
            'user_id' => $user->id,
            'price' => 99,
            'package_id' => 1,
            'attach_user_position' => 1,
            'attach_user_id' => 10,
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
        $order->setAttachUserId((int)$order_entity->attach_user_id);
        $order->setAttachUserPosition((int)$order_entity->attach_user_position);


        $this->assertNull($user->binaryTree);

        list($bool, $msg) = (new OrderResolver($order))->handle();
        $this->assertFalse($bool,$msg);

        $user->refresh();
        $this->assertNull($user->referralTree);


    }
}
