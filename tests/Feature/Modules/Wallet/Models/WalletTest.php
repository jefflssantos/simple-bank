<?php

namespace Tests\Feature\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Wallet\Models\Wallet;
use Tests\TestCase;

class WalletTest extends TestCase
{
    public function test_wallet_should_belongs_to_user(): void
    {
        $this->assertInstanceOf(BelongsTo::class, (new Wallet())->user());
    }
}
