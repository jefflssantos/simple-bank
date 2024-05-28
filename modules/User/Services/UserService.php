<?php

namespace Modules\User\Services;

use Modules\User\Enums\UserAccountTypeEnum;
use Modules\User\Models\User;

class UserService
{
    public function __construct(private readonly User $user)
    {
    }

    public function isRetailer(): bool
    {
        return $this->user->account_type === UserAccountTypeEnum::RETAILER;
    }
}
