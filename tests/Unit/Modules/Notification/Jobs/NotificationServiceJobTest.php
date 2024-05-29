<?php

namespace Tests\Unit\Modules\Notification\Jobs;

use Modules\Notification\Contracts\NotificationServiceContract;
use Modules\Notification\Jobs\NotificationServiceJob;
use Tests\TestCase;

class NotificationServiceJobTest extends TestCase
{
    public function test_it_should_dispatch_the_notification_service(): void
    {
        $notificationService = $this->mock(NotificationServiceContract::class);

        $notificationService
            ->shouldReceive('send')
            ->once();

        $notificationServiceJob = new NotificationServiceJob();
        $notificationServiceJob->handle($notificationService);
    }
}
