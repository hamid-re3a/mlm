<?php

namespace User\Observers;

use MLM\Jobs\Emails\EmailJob;
use MLM\Mail\UserRankChangedEmail;
use User\Models\User;

class UserObserver
{


    public function updating(User $user)
    {
            if($user->isDirty('rank')){
                EmailJob::dispatch(new UserRankChangedEmail($user),$user->email);
            }

    }

}
