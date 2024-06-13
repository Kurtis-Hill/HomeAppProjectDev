<?php

namespace App\DTOs\Logs;

class ElasticInfoLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'info';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
