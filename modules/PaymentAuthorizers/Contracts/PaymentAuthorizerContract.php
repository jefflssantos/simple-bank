<?php

namespace Modules\PaymentAuthorizers\Contracts;

interface PaymentAuthorizerContract
{
    public function isAuthorized(): bool;
}
