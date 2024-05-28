<?php

namespace App\Common\DTO\Logs;

class ElasticAlertLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'alert';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
