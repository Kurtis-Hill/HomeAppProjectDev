<?php

namespace App\DTOs\Logs;

interface ElasticLogDTOInterface
{
    public function getLevel(): string;

    public function getMessage(): string;

    public function getContext(): array;
}
