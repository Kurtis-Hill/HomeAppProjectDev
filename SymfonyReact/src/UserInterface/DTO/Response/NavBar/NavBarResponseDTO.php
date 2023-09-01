<?php

namespace App\UserInterface\DTO\Response\NavBar;

use App\Devices\DTO\Response\DeviceResponseDTO;
use App\User\DTO\Response\GroupDTOs\GroupResponseDTO;
use App\User\DTO\Response\RoomDTOs\RoomResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Ignore;

#[Immutable]
class NavBarResponseDTO
{
    private string $header;

    #[ArrayShape([NavBarListLinkDTO::class])]
    private array $listItemLinks;

    private string $icon;

    private string $itemName;

    #[ArrayShape(['string'])]
    private array $errors;

    public function __construct(string $header, string $icon, string $itemName, array $listItemLinks = [], array $errors = [])
    {
        $this->header = $header;
        $this->icon = $icon;
        $this->itemName = $itemName;
        $this->listItemLinks = $listItemLinks;
        $this->errors = $errors;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @return array
     */
    public function getListItemLinks(): array
    {
        return $this->listItemLinks;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }


//    #[ArrayShape([RoomResponseDTO::class || 'No Rooms Available'])]
//    private array $userRooms;
//
//    #[ArrayShape([DeviceResponseDTO::class || 'No Devices Available'])]
//    private array $devices;
//
//    #[ArrayShape([GroupNameResponseDTO::class || 'No GroupNames Available'])]
//    private array $groupNames;
//
//    #[Ignore]
//    #[ArrayShape(['errors'])]
//    private array $errors;
//
//    public function __construct(
//        array $userRooms,
//        array $devices,
//        array $groupNames,
//        array $errors = [],
//    ) {
//        $this->userRooms = $userRooms;
//        $this->devices = $devices;
//        $this->groupNames = $groupNames;
//        $this->errors = $errors;
//    }
//
//    public function getUserRooms(): array
//    {
//        return $this->userRooms;
//    }
//
//    public function getDevices(): array
//    {
//        return $this->devices;
//    }
//
//    public function getGroupNames(): array
//    {
//        return $this->groupNames;
//    }
//
}
