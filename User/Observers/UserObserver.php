<?php

namespace User\Observers;

use User\Mail\UserRankChangedEmail;
use User\Models\User;

class UserObserver
{
    public function updating(User $user)
    {


        if ($user->isDirty('rank')) {



        }

    }



}
