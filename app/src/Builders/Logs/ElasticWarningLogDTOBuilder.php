<?php

namespace App\Builders\Logs;

use App\DTOs\Logs\ElasticInfoLogDTO;
use App\DTOs\Logs\ElasticLogDTOInterface;

class ElasticWarningLogDTOBuilder implements ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface
    {
        return new ElasticInfoLogDTO(
            $message,
            $extraData
        );
    }
}
