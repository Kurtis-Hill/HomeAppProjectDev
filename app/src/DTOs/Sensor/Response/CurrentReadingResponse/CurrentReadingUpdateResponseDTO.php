<?php

namespace App\DTOs\Sensor\Response\CurrentReadingResponse;

class CurrentReadingUpdateResponseDTO
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
