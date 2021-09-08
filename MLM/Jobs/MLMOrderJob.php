<?php

namespace MLM\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Services\OrderResolver;
use Orders\Services\Order;
use User\Services\User;

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

        list($bool, $msg) = (new OrderResolver($order))->handle();
    }
}
