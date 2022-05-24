<?php

namespace App\UserInterface\DTO\Response\NavBar;

use App\Devices\DTO\Response\DeviceResponseDTO;
use App\User\DTO\ResponseDTOs\GroupDTOs\GroupNameDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class NavBarResponseDTO
{
    // @TODO create user room dto
    private array $userRooms;

    private DeviceResponseDTO $devices;

    private GroupNameDTO $groupNames;

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
