<?php

namespace App\Services\Common\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class HomeAppAltertSlackClient implements HomeAppAlertClientInterface
{
    public function __construct(
        private string $slackAlertWebhookUrl,
        private HttpClientInterface $httpClient,
    ) {
    }

    public function sendAlert(string $message): void
    {
        $this->httpClient->request(
            Request::METHOD_POST,
            $this->slackAlertWebhookUrl, [
            'json' => [
                'text' => $message,
            ],
        ]);
    }
}
