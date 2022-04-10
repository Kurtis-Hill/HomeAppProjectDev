<?php

namespace App\Authentication\DTOs\Response;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UserAuthenticationResponseDTO
{
    public function __construct(int $userID, array $roles)
    {
        $this->userID = $userID;
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }


}
