<?php

namespace App\Common\DTO\Logs;

interface ElasticLogDTOInterface
{
    public function getLevel(): string;

    public function getMessage(): string;

    public function getContext(): array;
}
