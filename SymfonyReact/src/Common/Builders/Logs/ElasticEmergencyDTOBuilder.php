<?php

namespace App\Common\Builders\Logs;

use App\Common\DTO\Logs\ElasticDebugLogDTO;
use App\Common\DTO\Logs\ElasticLogDTOInterface;

class ElasticEmergencyDTOBuilder implements ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface
    {
        return new ElasticDebugLogDTO(
            $message,
            $extraData
        );
    }
}
