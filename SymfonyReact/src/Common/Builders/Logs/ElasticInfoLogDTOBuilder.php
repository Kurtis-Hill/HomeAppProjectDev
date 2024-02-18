<?php

namespace App\Common\Builders\Logs;

use App\Common\DTO\Logs\ElasticInfoLogDTO;
use App\Common\DTO\Logs\ElasticLogDTOInterface;

class ElasticInfoLogDTOBuilder implements ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface
    {
        return new ElasticInfoLogDTO(
            $message,
            $extraData
        );
    }
}
