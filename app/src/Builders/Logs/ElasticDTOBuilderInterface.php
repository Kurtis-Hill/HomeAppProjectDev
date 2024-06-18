<?php

namespace App\Builders\Logs;

interface ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): \App\DTOs\Logs\ElasticLogDTOInterface;
}
