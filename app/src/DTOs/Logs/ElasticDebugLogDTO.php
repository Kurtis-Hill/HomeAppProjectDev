<?php

namespace App\DTOs\Logs;

class ElasticDebugLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'debug';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
