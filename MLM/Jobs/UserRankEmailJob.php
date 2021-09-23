<?php

namespace MLM\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use MLM\Mail\SettingableMail;
use MLM\Mail\UserRankChangedEmail;
use User\Models\User;


class UserRankEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function handle()
    {
        Mail::to($this->user->email)->send( new UserRankChangedEmail($this->user));

    }

}
