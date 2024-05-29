<?php

namespace Tests\Feature\Modules\Transaction\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Modules\Notification\Jobs\NotificationServiceJob;
use Modules\User\Models\User;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    public function test_it_should_allow_consumers_to_transfer_to_sellers(): void
    {
        Queue::fake();
        Http::fake([
            '*' => Http::response(
                '{"status" : "success", "data" : { "authorization" : true }}',
                200
            ),
        ]);

        $transferAmount = 42.00;
        $payer = User::factory()->consumer()->create();
        $payee = User::factory()->seller()->create();
        $payer->wallet->credit(42_00);

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertNoContent();

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('transactions', [
            'payer_wallet_id' => $payer->wallet->id,
            'payee_wallet_id' => $payee->wallet->id,
            'amount' => 42_00,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 0,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 42_00,
        ]);

        Queue::assertPushed(NotificationServiceJob::class);
    }

    public function test_it_should_allow_consumers_to_transfer_to_consumers(): void
    {
        Queue::fake();
        Http::fake([
            '*' => Http::response(
                '{"status" : "success", "data" : { "authorization" : true }}',
                200
            ),
        ]);

        $transferAmount = 42.00;
        $payer = User::factory()->consumer()->create();
        $payee = User::factory()->consumer()->create();
        $payer->wallet->credit(42_00);

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertNoContent();

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('transactions', [
            'payer_wallet_id' => $payer->wallet->id,
            'payee_wallet_id' => $payee->wallet->id,
            'amount' => 42_00,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 0,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 42_00,
        ]);

        Queue::assertPushed(NotificationServiceJob::class);
    }

    public function test_it_should_not_allow_sellers_make_a_transfer(): void
    {
        Queue::fake();

        $transferAmount = 42.00;
        $payer = User::factory()->seller()->create();
        $payee = User::factory()->consumer()->create();
        $payer->wallet->credit(42_00);

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertPaymentRequired()
            ->assertExactJson(['message' => 'Sellers are not allowed to transfer.']);

        $this->assertDatabaseEmpty('transactions');
        $this->assertDatabaseHas('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 42_00,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 0,
        ]);

        Queue::assertNotPushed(NotificationServiceJob::class);
    }

    public function test_it_should_fail_when_transfer_to_the_same_user(): void
    {
        Queue::fake();

        $payer = User::factory()->consumer()->create();

        $response = $this->postJson(route('transfer'), [
            'value' => 10,
            'payer' => $payer->id,
            'payee' => $payer->id,
        ]);

        $response->assertPaymentRequired()
            ->assertExactJson(['message' => 'Transfer not allowed to the same person.']);

        $this->assertDatabaseEmpty('transactions');
        $this->assertDatabaseHas('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 0,
        ]);

        Queue::assertNotPushed(NotificationServiceJob::class);
    }

    public function test_it_should_fail_when_transfer_with_invalid_amount(): void
    {
        Queue::fake();

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

        $this->assertDatabaseEmpty('transactions');
        $this->assertDatabaseHas('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 0,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 0,
        ]);

        Queue::assertNotPushed(NotificationServiceJob::class);
    }

    public function test_it_should_fail_when_the_payer_has_insufficient_balance(): void
    {
        Queue::fake();

        $transferAmount = 42.00;
        $payer = User::factory()->consumer()->create();
        $payer->wallet->credit(40_00);
        $payee = User::factory()->seller()->create();

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertPaymentRequired()
            ->assertExactJson(['message' => 'Insufficient balance.']);

        $this->assertDatabaseEmpty('transactions');
        $this->assertDatabaseHas('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 40_00,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 0,
        ]);
        Queue::assertNotPushed(NotificationServiceJob::class);
    }

    public function test_it_should_fail_when_the_transfer_is_not_authorized(): void
    {
        Queue::fake();
        Http::fake([
            '*' => Http::response(
                '{"status" : "failed", "data" : { "authorization" : false }}',
                403
            ),
        ]);

        $transferAmount = 42.00;
        $payer = User::factory()->consumer()->create();
        $payee = User::factory()->consumer()->create();
        $payer->wallet->credit(42_00);

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertPaymentRequired()
            ->assertExactJson(['message' => 'Transfer not allowed by the payment authorizer.']);

        $this->assertDatabaseEmpty('transactions');
        $this->assertDatabaseHas('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 42_00,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 0,
        ]);

        Queue::assertNotPushed(NotificationServiceJob::class);
    }

    public function test_it_should_fail_when_the_authorizer_request_failed(): void
    {
        Queue::fake();
        Http::fake(['*' => Http::response('Server error', 500)]);

        $transferAmount = 42.00;
        $payer = User::factory()->consumer()->create();
        $payer->wallet->credit(42_00);
        $payee = User::factory()->seller()->create();

        $response = $this->postJson(route('transfer'), [
            'value' => $transferAmount,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        $response->assertPaymentRequired()
            ->assertExactJson(['message' => 'Transfer not allowed by the payment authorizer.']);

        $this->assertDatabaseEmpty('transactions');
        $this->assertDatabaseHas('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 42_00,
        ]);
        $this->assertDatabaseHas('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 0,
        ]);

        Queue::assertNotPushed(NotificationServiceJob::class);
    }
}
