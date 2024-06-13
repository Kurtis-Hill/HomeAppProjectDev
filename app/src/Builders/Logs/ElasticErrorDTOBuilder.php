<?php

namespace App\Builders\Logs;

class ElasticErrorDTOBuilder implements ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): \App\DTOs\Logs\ElasticLogDTOInterface
    {
        return new \App\DTOs\Logs\ElasticErrorLogDTO(
            $message,
            $extraData
        );
    }
}
