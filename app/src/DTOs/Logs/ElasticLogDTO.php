<?php

namespace App\DTOs\Logs;

class ElasticLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'log';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
