<?php

namespace App\Sensors\Clients\ElasticSearch;

use Elastica\Client;
use JetBrains\PhpStorm\ArrayShape;

class ElasticSearchClient
{
    private static ?Client $elasticClient = null;

    private string $host;

    private int $port;

    private string $user;

    private string $password;

    private string $transport;

    private bool $sslVerifyPeer;

    private ?string $failOverHost;

    private ?int $failOverPort;

    private ?string $failOverTransport;

    private ?bool $failOverSslVerifyPeer;

    private ?string $failOverUser;

    private ?string $failOverPassword;

    public function __construct(
        string $host,
        int $port,
        string $transport,
        bool $sslVerification,
        ?string $user,
        ?string $password,
        ?string $failOverHost,
        ?int $failOverPort,
        ?string $failOverTransport,
        ?bool $failOverSslVerification,
        ?string $failOverUser,
        ?string $failOverPassword
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->transport = $transport;
        $this->sslVerifyPeer = $sslVerification;
        $this->user = $user;
        $this->password = $password;
        $this->failOverHost = $failOverHost;
        $this->failOverPort = $failOverPort;
        $this->failOverTransport = $failOverTransport;
        $this->failOverSslVerifyPeer = $failOverSslVerification;
        $this->failOverUser = $failOverUser;
        $this->failOverPassword = $failOverPassword;
    }

    public function getElasticsearchClient(): Client
    {
        if (self::$elasticClient === null) {
            self::$elasticClient = $this->createElasticClient();
        }

        return self::$elasticClient;
    }

    private function createElasticClient(): Client
    {
        $elasticServerConfig[] = self::buildElasticServerConfig(
            $this->host,
            $this->port,
            $this->transport,
            $this->sslVerifyPeer,
            $this->user,
            $this->password
        );
        if ($this->failOverHost !== null) {
            $elasticServerConfig[] = self::buildElasticServerConfig(
                $this->failOverHost,
                $this->failOverPort,
                $this->failOverTransport,
                $this->failOverSslVerifyPeer,
                $this->failOverUser,
                $this->failOverPassword
            );
        }

        return new Client([
            'servers' => $elasticServerConfig,
        ]);
    }

    #[ArrayShape([
        'host' => "string",
        'port' => "int",
        'transport' => "string",
        'sslVerification' => "bool",
        'user' => "string",
        'password' => "string",
    ])]
    private static function buildElasticServerConfig(
        string $host,
        int $port,
        string $transport,
        bool $sslVerification,
        ?string $user,
        ?string $password
    ): array {
        return [
            'host' => $host,
            'port' => $port,
            'curl' => [
                CURLOPT_USERPWD => $user . ':' . $password,
                CURLOPT_SSL_VERIFYPEER => $sslVerification,
            ],
            'transport' => $transport,
        ];
    }
}
