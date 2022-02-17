<?php

namespace App\Devices\DTO\ResponseDTO;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class NewDeviceSuccessResponseDTO
{
    private string $secret;

    private int $deviceID;

    public function __construct(
        string $secret,
        int $deviceID
    ) {
        $this->secret = $secret;
        $this->deviceID = $deviceID;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getDeviceID(): int
    {
        return $this->deviceID;
    }
}
