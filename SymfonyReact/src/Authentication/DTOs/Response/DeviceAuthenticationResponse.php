<?php
declare(strict_types=1);

namespace App\Authentication\DTOs\Response;

use App\Authentication\DTOs\Request\DeviceAuthenticationIPRequestDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class DeviceAuthenticationResponse
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
