<?php

namespace Modules\Transaction\Services;

use Illuminate\Support\Facades\DB;
use Modules\PaymentAuthorizers\Contracts\PaymentAuthorizerContract;
use Modules\Transaction\DTOs\TransactionDTO;
use Modules\Transaction\Exceptions\TransactionException;
use Modules\Transaction\Models\Transaction;
use Modules\User\Models\User;

class TransactionService
{
    public function __construct(
        private readonly PaymentAuthorizerContract $paymentAuthorizer
    ) {
    }

    public function transfer(TransactionDTO $transactionData): Transaction
    {
        $payer = User::with('wallet')->findOrFail($transactionData->payerId);
        $payee = User::with('wallet')->findOrFail($transactionData->payeeId);

        $this->validateTransferConditions($payer, $payee, $transactionData);

        return DB::transaction(function () use ($payer, $payee, $transactionData) {
            $payer->wallet->debit($transactionData->amount);
            $payee->wallet->credit($transactionData->amount);

            $transaction = Transaction::create([
                'payer_wallet_id' => $payer->wallet->id,
                'payee_wallet_id' => $payee->wallet->id,
                'amount' => $transactionData->amount,
            ]);

            if (! $this->paymentAuthorizer->isAuthorized()) {
                throw new TransactionException(
                    'Transfer not allowed by the payment authorizer.'
                );
            }

            return $transaction;
        });
    }

    public function validateTransferConditions(
        User $payer, User $payee, TransactionDTO $transactionData
    ): void {
        if (! is_int($transactionData->amount) || $transactionData->amount < 1) {
            throw new TransactionException('Invalid transfer amount.');
        }

        if ($payer->id === $payee->id) {
            throw new TransactionException('Transfer not allowed to the same person.');
        }

        if (! $payer->wallet->hasBalance($transactionData->amount)) {
            throw new TransactionException('Insufficient balance.');
        }

        if ($payer->isSeller()) {
            throw new TransactionException('Sellers are not allowed to transfer.');
        }
    }
}
