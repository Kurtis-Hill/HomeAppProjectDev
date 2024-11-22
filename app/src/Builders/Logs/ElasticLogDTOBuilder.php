<?php

namespace App\Builders\Logs;

use App\DTOs\Logs\ElasticLogDTO;
use App\DTOs\Logs\ElasticLogDTOInterface;

class ElasticLogDTOBuilder
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface
    {
        return new ElasticLogDTO(
            $message,
            $extraData
        );
    }
}
