<?php

namespace App\Builders\Logs;

use App\DTOs\Logs\ElasticLogDTOInterface;

interface ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface;
}
