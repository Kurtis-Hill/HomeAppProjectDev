<?php

namespace App\Authentication\DTOs\Response;

use App\Authentication\DTOs\Request\DeviceAuthenticationIPRequestDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class DeviceAuthenticationResponse
{
    private string $token;

    private DeviceAuthenticationIPRequestDTO $deviceIps;

    public function __construct(string $token, ?DeviceAuthenticationIPRequestDTO $deviceIps = null)
    {
        $this->token = $token;
        $this->deviceIps = $deviceIps;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getDeviceIps(): DeviceAuthenticationIPRequestDTO
    {
        return $this->deviceIps;
    }
}
