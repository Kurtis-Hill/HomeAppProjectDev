<?php

namespace App\UserInterface\DTO\Response\NavBar;

use App\Devices\DTO\Response\DeviceResponseDTO;
use App\User\DTO\ResponseDTOs\GroupDTOs\GroupNameResponseDTO;
use App\User\DTO\ResponseDTOs\RoomDTOs\RoomResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class NavBarResponseDTO
{
    #[ArrayShape([RoomResponseDTO::class || 'No Rooms Available'])]
    private array $userRooms;

    #[ArrayShape([DeviceResponseDTO::class || 'No Devices Available'])]
    private array $devices;

    #[ArrayShape([GroupNameResponseDTO::class || 'No GroupNames Available'])]
    private array $groupNames;

    #[ArrayShape(['errors'])]
    private array $errors;

    public function __construct(
        array $userRooms,
        array $devices,
        array $groupNames,
        array $errors,
    ) {
        $this->userRooms = $userRooms;
        $this->devices = $devices;
        $this->groupNames = $groupNames;
        $this->errors = $errors;
    }

    public function getUserRooms(): array
    {
        return $this->userRooms;
    }

    public function getDevices(): array
    {
        return $this->devices;
    }

    public function getGroupNames(): array
    {
        return $this->groupNames;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
