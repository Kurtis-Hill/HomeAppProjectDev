<?php

namespace App\Builders\Logs;


use App\DTOs\Logs\ElasticLogDTOInterface;
use App\DTOs\Logs\ElasticNoticeLogDTO;

class ElasticNoticeLogDTOBuilder implements ElasticDTOBuilderInterface
{
    public function buildLogDTO(string $message, array $extraData): ElasticLogDTOInterface
    {
        return new ElasticNoticeLogDTO(
            $message,
            $extraData
        );
    }
}
