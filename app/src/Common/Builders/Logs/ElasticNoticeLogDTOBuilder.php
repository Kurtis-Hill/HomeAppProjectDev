<?php

namespace App\Common\Builders\Logs;


use App\Common\DTO\Logs\ElasticLogDTOInterface;
use App\Common\DTO\Logs\ElasticNoticeLogDTO;

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
