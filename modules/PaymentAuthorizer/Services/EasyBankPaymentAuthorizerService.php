<?php

namespace Modules\PaymentAuthorizer\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\PaymentAuthorizer\Contracts\PaymentAuthorizerContract;

class EasyBankPaymentAuthorizerService implements PaymentAuthorizerContract
{
    public function isAuthorized(): bool
    {
        try {
            $response = Http::retry(times: 3, sleepMilliseconds: 100)->get(
                config('authorizer.endpoint')
            );
        } catch (RequestException $e) {
            if (! $e->response->forbidden()) {
                Log::error('Payment authorizer error', ['message' => $e->getMessage()]);
            }

            return false;
        }

        return $response['data']['authorization'];
    }
}
