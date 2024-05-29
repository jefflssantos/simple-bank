<?php

namespace Modules\Notification\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Notification\Contracts\NotificationServiceContract;

class ResendNotificationService implements NotificationServiceContract
{
    public function send(): bool
    {
        $response = Http::post(config('notification.endpoint'));

        if (! $response->successful()) {
            Log::error(
                'Notification service error',
                ['message' => $response->body()]
            );

            $response->throw();
        }

        return true;
    }
}
