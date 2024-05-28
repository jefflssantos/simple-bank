<?php

namespace Tests\Feature\Modules\User\Services;

use Modules\User\Enums\UserAccountTypeEnum;
use Modules\User\Models\User;
use Modules\User\Services\UserService;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    public function test_should_check_if_user_is_retailer(): void
    {
        $user = User::factory()->create([
            'account_type' => UserAccountTypeEnum::RETAILER,
        ]);

        $userService = new UserService($user);

        $this->assertTrue($userService->isRetailer());
    }

    public function test_should_check_if_user_is_not_retailer(): void
    {
        $user = User::factory()->create([
            'account_type' => UserAccountTypeEnum::CUSTOMER,
        ]);

        $userService = new UserService($user);

        $this->assertFalse($userService->isRetailer());
    }
}
