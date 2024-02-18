<?php

namespace App\Common\DTO\Logs;

class ElasticNoticeLogDTO extends AbstractElasticLogDTO implements ElasticLogDTOInterface
{
    private const LEVEL = 'notice';

    public function getLevel(): string
    {
        return self::LEVEL;
    }
}
