<?php

namespace App\Common\DTO\Logs;

use Stringable;

class AbstractElasticLogDTO
{
    protected string|Stringable $message;

    protected array $context;

    public function __construct(string|Stringable $message, array $context)
    {
        $this->message = $message;
        $this->context = $context;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
