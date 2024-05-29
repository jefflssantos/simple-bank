<?php

namespace Modules\Notification\Contracts;

interface NotificationServiceContract
{
    public function send(): bool;
}
