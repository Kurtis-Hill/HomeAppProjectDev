<?php

namespace App\Authentication\DTOs\Response;

use App\Authentication\DTOs\Request\DeviceAuthenticationIPRequestDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class DeviceAuthenticationResponse
{
    private string $token;

//    private string $refreshToken;

    private DeviceAuthenticationIPRequestDTO $deviceIps;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

//    public function getRefreshToken(): string
//    {
//        return $this->refreshToken;
//    }
}
