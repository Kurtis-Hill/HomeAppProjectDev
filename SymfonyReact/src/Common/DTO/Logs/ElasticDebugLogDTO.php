<?php

namespace App\Common\DTO\Logs;

class ElasticDebugLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'debug';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
