<?php

namespace App\Common\DTO\Logs;

class ElasticLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'log';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
