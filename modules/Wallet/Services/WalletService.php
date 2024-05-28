<?php

namespace Modules\Wallet\Services;

use Modules\Wallet\Models\Wallet;

class WalletService
{
    public function __construct(private readonly Wallet $wallet)
    {}

    public function credit(int $value): bool
    {
        return $this->wallet->update([
            'balance' => $this->wallet->balance + $value
        ]);
    }

    public function debit(int $value): bool
    {
        return $this->wallet->update([
            'balance' => $this->wallet->balance - $value
        ]);
    }
}
