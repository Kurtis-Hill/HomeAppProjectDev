<?php

namespace App\DTOs\Authentication\Response;

readonly class UserDataDTO
{
    private int $userID;

    private array $roles;

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
