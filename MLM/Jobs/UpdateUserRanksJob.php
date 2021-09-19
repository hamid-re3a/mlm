<?php

namespace MLM\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Models\OrderedPackage;
use MLM\Models\Rank;
use User\Models\User;
use User\Services\UserService;
use Wallets\Services\Grpc\Deposit;

class UpdateUserRanksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $package;
    private $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(UserService $user_service)
    {
        getAndUpdateUserRank($this->user);
        if (!is_null($this->user->referralTree->parent) && !is_null($this->user->referralTree->parent->user_id)) {
            $parent = $user_service->findByIdOrFail($this->user->referralTree->parent->user_id);
            UpdateUserRanksJob::dispatch($parent);
        }
    }

}
