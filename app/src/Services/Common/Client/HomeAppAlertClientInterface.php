<?php

namespace App\Services\Common\Client;

interface HomeAppAlertClientInterface
{
    public function sendAlert(string $message): void;
}
