<?php

namespace App\DTOs\Logs;

class ElasticCriticalLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'critical';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
