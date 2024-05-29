<?php

namespace Modules\Transaction\DTOs;

readonly class TransactionDTO
{
    public function __construct(
        public int $amount,
        public int $payerId,
        public int $payeeId
    ) {
    }
}
