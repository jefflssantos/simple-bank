<?php

namespace Tests\Feature\Modules\Transactions\Http\Controllers;

use Modules\User\Models\User;
use Modules\Wallet\Models\Wallet;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    public function test_consumers_should_be_able_to_transfer_to_sellers(): void
    {
        $transferAmount = 42.00;
        $payer = User::factory()->consumer()->create();
        $payerWallet = Wallet::factory()->for($payer)->create(['balance' => 42_00]);
        $payee = User::factory()->seller()->create();
        $payeeWallet = Wallet::factory()->for($payee)->create(['balance' => 0]);

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertNoContent();

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('transactions', [
            'payer_wallet_id' => $payerWallet->id,
            'payee_wallet_id' => $payeeWallet->id,
            'amount' => 42_00,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payerWallet->id,
            'balance' => 0,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payeeWallet->id,
            'balance' => 42_00,
        ]);
    }

    public function test_consumers_should_be_able_to_transfer_to_consumers(): void
    {
        $transferAmount = 42.00;
        $payer = User::factory()->consumer()->create();
        $payerWallet = Wallet::factory()->for($payer)->create(['balance' => 42_00]);
        $payee = User::factory()->consumer()->create();
        $payeeWallet = Wallet::factory()->for($payee)->create(['balance' => 0]);

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertNoContent();

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('transactions', [
            'payer_wallet_id' => $payerWallet->id,
            'payee_wallet_id' => $payeeWallet->id,
            'amount' => 42_00,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payerWallet->id,
            'balance' => 0,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payeeWallet->id,
            'balance' => 42_00,
        ]);
    }

    public function test_sellers_are_not_allowed_to_make_a_transfer(): void
    {
        $transferAmount = 42.00;
        $payer = User::factory()->seller()->create();
        Wallet::factory()->for($payer)->create(['balance' => 42_00]);
        $payee = User::factory()->seller()->create();
        Wallet::factory()->for($payee)->create(['balance' => 0]);

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertPaymentRequired()
            ->assertExactJson(['message' => 'Sellers are not allowed to transfer.']);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_it_should_fail_when_transfer_to_the_same_person(): void
    {
        $payer = User::factory()->consumer()->create();

        $response = $this->postJson(route('transfer'), [
            'value' => 10,
            'payer' => $payer->id,
            'payee' => $payer->id,
        ]);

        $response->assertPaymentRequired()
            ->assertExactJson(['message' => 'Transfer not allowed to the same person.']);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_it_should_fail_when_transfer_invalid_amount(): void
    {
        $transferAmount = 0.00;
        $payer = User::factory()->consumer()->create();
        $payee = User::factory()->seller()->create();

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertPaymentRequired()
            ->assertExactJson(['message' => 'Invalid transfer amount.']);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_it_should_fail_when_the_payer_has_insufficient_balance(): void
    {
        $transferAmount = 42.00;
        $payer = User::factory()->consumer()->create();
        Wallet::factory()->for($payer)->create(['balance' => 0]);
        $payee = User::factory()->seller()->create();

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertPaymentRequired()
            ->assertExactJson(['message' => 'Insufficient balance.']);

        $this->assertDatabaseCount('transactions', 0);
    }
}
