<?php

namespace App\UserInterface\DTO\Response\UserData;

use App\User\DTO\Response\RoomDTOs\RoomResponseDTO;
use App\User\Entity\GroupNames;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UserDataResponseDTO
{
    #[ArrayShape([RoomResponseDTO::class])]
    private array $userRooms;

    #[ArrayShape([GroupNames::class])]
    private array $userGroups;

    public function __construct(
        array $userRooms,
        array $userGroups,
    ){
        $this->userRooms = $userRooms;
        $this->userGroups = $userGroups;
    }

    public function getUserRooms(): array
    {
        return $this->userRooms;
    }

    public function getUserGroups(): array
    {
        return $this->userGroups;
    }
}
