<?php

namespace App\Builders\Logs;

use App\DTOs\Logs\ElasticDebugLogDTO;
use App\DTOs\Logs\ElasticLogDTOInterface;

class ElasticDebugDTOBuilder implements ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface
    {
        return new ElasticDebugLogDTO(
            $message,
            $extraData
        );
    }
}
