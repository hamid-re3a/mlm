<?php

namespace User\Observers;

use MLM\Jobs\Emails\UrgentEmailJob;
use MLM\Mail\UserRankChangedEmail;
use User\Models\User;

class UserObserver
{


    public function updating(User $user)
    {
            if($user->isDirty('rank')){
                UrgentEmailJob::dispatch(new UserRankChangedEmail($user),$user->email);
            }

    }

}
