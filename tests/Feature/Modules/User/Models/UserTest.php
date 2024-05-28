<?php

namespace Tests\Feature\Modules\User\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\User\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_should_have_one_wallet(): void
    {
        $this->assertInstanceOf(HasOne::class, (new User())->wallet());
    }
}
