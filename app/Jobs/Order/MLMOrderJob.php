<?php

namespace App\Jobs\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use MLM\Services\OrderResolver;
use Orders\Services\Grpc\Order;
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


    public function handle(OrderService $order_service)
    {
        /** @var Order $order */
        $order = unserialize($this->data);

        $order_service->updateOrderAndPackage($order);

        list($bool, $msg) = (new OrderResolver($order))->handle();
        if ($bool) {
            try {
                MLMOrderJob::dispatch($order)->onConnection('rabbit')->onQueue('subscription');
            } catch (\Exception $e) {
                Log::error('Order didnt update the subscription');
                Log::error($e->getMessage());
            }
        }
    }
}
