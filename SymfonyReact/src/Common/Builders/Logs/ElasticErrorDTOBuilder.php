<?php

namespace App\Common\Builders\Logs;

use App\Common\DTO\Logs\ElasticErrorLogDTO;
use App\Common\DTO\Logs\ElasticLogDTOInterface;

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
