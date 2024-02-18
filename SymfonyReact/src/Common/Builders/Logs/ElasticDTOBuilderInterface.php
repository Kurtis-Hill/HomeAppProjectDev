<?php

namespace App\Common\Builders\Logs;

use App\Common\DTO\Logs\ElasticLogDTOInterface;

interface ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface;
}
