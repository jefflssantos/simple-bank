<?php

namespace Modules\User\Observers;

use Modules\User\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $user->wallet()->create();
    }
}
