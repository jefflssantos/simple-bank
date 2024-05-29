<?php

namespace Modules\Notification\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Notification\Contracts\NotificationServiceContract;
use Modules\Notification\Services\ResendNotificationService;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(
            NotificationServiceContract::class,
            ResendNotificationService::class
        );
    }
}
