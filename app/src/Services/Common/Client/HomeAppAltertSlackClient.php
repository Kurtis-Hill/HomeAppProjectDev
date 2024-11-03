<?php

namespace App\Services\Common\Client;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class HomeAppAltertSlackClient implements HomeAppAlertClientInterface
{
    public function __construct(
        private string $slackAlertWebhookUrl,
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
    ) {
    }

    public function sendAlert(string $message): void
    {
        $this->logger->info(
            sprintf(
                'Sending alert to slack with message: %s to url %s',
                $message,
                $this->slackAlertWebhookUrl
            ),
            ['message' => $message]
        );

        $this->httpClient->request(
            Request::METHOD_POST,
            $this->slackAlertWebhookUrl, [
            'json' => [
                'text' => $message,
            ],
        ]);
    }
}
