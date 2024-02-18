<?php

namespace App\Common\DTO\Logs;

class ElasticErrorLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'error';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
