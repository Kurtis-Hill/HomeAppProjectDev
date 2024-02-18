<?php

namespace App\Common\Builders\Logs;

use App\Common\DTO\Logs\ElasticLogDTO;
use App\Common\DTO\Logs\ElasticLogDTOInterface;

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
