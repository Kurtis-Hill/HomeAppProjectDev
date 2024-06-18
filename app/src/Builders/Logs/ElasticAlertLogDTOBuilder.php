<?php

namespace App\Builders\Logs;

use App\DTOs\Logs\ElasticAlertLogDTO;
use App\DTOs\Logs\ElasticLogDTOInterface;

class ElasticAlertLogDTOBuilder implements ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface
    {
        return new ElasticAlertLogDTO(
            $message,
            $extraData
        );
    }
}
