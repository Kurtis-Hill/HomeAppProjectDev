<?php

namespace App\DTOs\Logs;

class ElasticEmergencyLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'emergency';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
