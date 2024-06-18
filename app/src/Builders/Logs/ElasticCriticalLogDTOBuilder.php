<?php

namespace App\Builders\Logs;

use App\DTOs\Logs\ElasticLogDTOInterface;

class ElasticCriticalLogDTOBuilder implements ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface
    {
        return new \App\DTOs\Logs\ElasticCriticalLogDTO(
            $message,
            $extraData
        );
    }
}
