<?php

namespace App\Common\Builders\Logs;

use App\Common\DTO\Logs\ElasticAlertLogDTO;
use App\Common\DTO\Logs\ElasticLogDTOInterface;

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
