<?php

namespace App\UserInterface\DTO\Response\UserData;

use App\User\DTO\ResponseDTOs\RoomDTOs\RoomResponseDTO;
use App\User\Entity\GroupNames;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UserDataResponseDTO
{
    #[ArrayShape([RoomResponseDTO::class])]
    private array $userRoomDTOs;

    #[ArrayShape([GroupNames::class])]
    private array $groupNameDTOs;

    public function __construct(
        array $userRoomDTOs,
        array $groupNameDTOs,
    ){
        $this->userRoomDTOs = $userRoomDTOs;
        $this->groupNameDTOs = $groupNameDTOs;
    }

    public function getUserRoomDTOs(): array
    {
        return $this->userRoomDTOs;
    }

    public function getGroupNameDTOs(): array
    {
        return $this->groupNameDTOs;
    }
}
