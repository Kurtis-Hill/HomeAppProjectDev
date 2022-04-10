<?php

namespace App\Authentication\DTOs\Response;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UserAuthenticationResponseDTO
{
    private UserDataDTO $userData;

    private string $token;

    public function __construct(UserDataDTO $userDataDTO, string $token)
    {
        $this->userData = $userDataDTO;
        $this->token = $token;
    }

    public function getUserData(): UserDataDTO
    {
        return $this->userData;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
