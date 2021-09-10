<?php

namespace App\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Services\OrderResolver;
use Orders\Services\Order;
use Orders\Services\OrderService;

class MLMOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }


    public function handle()
    {
        /** @var Order $order */
        $order = unserialize($this->data);

        /** @var  $order_service OrderService*/
        $order_service = app(OrderService::class);
        $order_service->updateOrder($order);

        list($bool, $msg) = (new OrderResolver($order))->handle();

//        MLMOrderJob::dispatch()->
    }
}
