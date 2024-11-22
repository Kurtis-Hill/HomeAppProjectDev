<?php

namespace App\Builders\Logs;

use App\DTOs\Logs\ElasticErrorLogDTO;
use App\DTOs\Logs\ElasticLogDTOInterface;

class ElasticErrorDTOBuilder implements ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface
    {
        return new ElasticErrorLogDTO(
            $message,
            $extraData
        );
    }
}
