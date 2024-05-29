<?php

namespace Tests\Unit\Modules\Notification\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Modules\Notification\Services\ResendNotificationService;
use Tests\TestCase;

class ResendNotificationServiceTest extends TestCase
{
    public function test_it_should_send_the_notification(): void
    {
        Http::fake();

        $resendNotificationService = new ResendNotificationService();
        $resendNotificationService->send();

        $this->assertTrue($resendNotificationService->send());
    }

    public function test_it_should_throw_exception_when_have_a_server_error(): void
    {
        Http::fake(['*' => Http::response('', 500)]);

        $resendNotificationService = new ResendNotificationService();

        $this->assertThrows(fn () => $resendNotificationService->send(), RequestException::class);
    }
}
