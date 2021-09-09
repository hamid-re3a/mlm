<?php

namespace MLM\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use User\Models\User;

class TradingProfitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        if($this->user->commissions()->where('type',TRADING_PROFIT_COMMISSION)
            ->whereDate('created_at','>=',now()->addMonth()->toDate())->exists())
            return;


    }
}
