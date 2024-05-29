<?php

namespace Modules\PaymentAuthorizer\Contracts;

interface PaymentAuthorizerContract
{
    public function isAuthorized(): bool;
}
