<?php
namespace MLM\tests\UnitTest;

use MLM\tests\MLMTest;
use Orders\Services\Order;
use User\Services\User;

class MLMUnitTest extends MLMTest
{

    /**
     * @test
     */
    public function it_can_process_incoming_order()
    {

        $order = new Order();
        $order->setId(1);
        $order->setUserId(1);
        $order->setTotalCostInUsd(199);
        $order->setIsPaidAt(now()->toString());
        $order->setIsResolvedAt(now()->toString());
        $order->setPlan('');
        $order->setDeletedAt(null);
        $order->setCreatedAt(now()->toString());
        $order->setUpdatedAt(now()->toString());

        $order->setUser(user(1));

        \MLM\Jobs\TradingProfitCommissionJob::dispatch($order);




    }


}
