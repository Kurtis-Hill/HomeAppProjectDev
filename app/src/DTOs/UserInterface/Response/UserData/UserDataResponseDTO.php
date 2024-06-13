<?php

namespace App\DTOs\UserInterface\Response\UserData;

use App\DTOs\User\Response\RoomDTOs\RoomResponseDTO;
use App\Entity\User\Group;
use App\Services\Request\RequestTypeEnum;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
class UserDataResponseDTO
{
    #[ArrayShape([RoomResponseDTO::class])]
    private array $userRooms;

    #[ArrayShape([Group::class])]
    private array $userGroups;

    public function __construct(
        array $userRooms,
        array $userGroups,
    ){
        $this->userRooms = $userRooms;
        $this->userGroups = $userGroups;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getUserRooms(): array
    {
        return $this->userRooms;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getUserGroups(): array
    {
        return $this->userGroups;
    }
}
