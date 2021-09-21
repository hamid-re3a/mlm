<?php

namespace User\Observers;

use MLM\Jobs\UserRankEmailJob;
use MLM\Mail\UserRankChangedEmail;
use User\Models\User;

class UserObserver
{


    public function updating(User $user)
    {

            if($user->isDirty('rank')){

                UserRankEmailJob::dispatch($user);

            }

    }

}
