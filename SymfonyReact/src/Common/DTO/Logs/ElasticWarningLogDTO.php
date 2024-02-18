<?php

namespace App\Common\DTO\Logs;

class ElasticWarningLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'warning';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
