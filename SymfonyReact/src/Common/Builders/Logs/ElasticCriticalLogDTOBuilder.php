<?php

namespace App\Common\Builders\Logs;

use App\Common\DTO\Logs\ElasticCriticalLogDTO;
use App\Common\DTO\Logs\ElasticLogDTOInterface;

class ElasticCriticalLogDTOBuilder implements ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface
    {
        return new ElasticCriticalLogDTO(
            $message,
            $extraData
        );
    }
}
