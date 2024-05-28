<?php

namespace Tests\Feature\Modules\Wallet\Services;

use Modules\User\Models\User;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Services\WalletService;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    public function test_it_should_credit_wallet_balance(): void
    {
        $currentBalance = 100;
        $creditAmount = 30;
        $expectedBalance = 130;
        $user = User::factory()->create();
        $wallet = Wallet::factory()->for($user)->create([
            'balance' => $currentBalance
        ]);
        $walletService = new WalletService($wallet);

        $walletService->credit($creditAmount);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => $expectedBalance
        ]);
    }

    public function test_it_should_debit_wallet_balance(): void
    {
        $currentBalance = 100;
        $creditAmount = 30;
        $expectedBalance = 70;
        $user = User::factory()->create();
        $wallet = Wallet::factory()->for($user)->create([
            'balance' => $currentBalance
        ]);
        $walletService = new WalletService($wallet);

        $walletService->debit($creditAmount);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => $expectedBalance
        ]);
    }
}
