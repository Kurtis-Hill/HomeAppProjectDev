<?php

namespace App\Builders\Logs;

class ElasticLogDTOBuilder
{
    public function buildLogDTO(string $message, array $extraData): \App\DTOs\Logs\ElasticLogDTOInterface
    {
        return new \App\DTOs\Logs\ElasticLogDTO(
            $message,
            $extraData
        );
    }
}
