<?php

namespace App\Devices\DTO\Response;

use App\Common\Builders\Request\RequestDTOBuilder;
use App\Devices\Entity\Devices;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\StandardReadingTypeResponseInterface;
use App\User\DTO\ResponseDTOs\GroupDTOs\GroupNameResponseDTO;
use App\User\DTO\ResponseDTOs\RoomDTOs\RoomResponseDTO;
use Symfony\Component\Serializer\Annotation\Groups;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class DeviceFullDetailsResponseDTO
{
    public function __construct(
        private int $deviceNameID,
        private string $deviceName,
        private ?string $secret,
        private GroupNameResponseDTO $groupName,
        private RoomResponseDTO $room,
        private ?string $ipAddress,
        private ?string $externalIpAddress,
        private array $roles,
        private array $sensorsData,
    ) {
    }

    #[Groups([RequestDTOBuilder::REQUEST_TYPE_FULL])]
    public function getDeviceNameID(): int
    {
        return $this->deviceNameID;
    }

    #[Groups([RequestDTOBuilder::REQUEST_TYPE_FULL])]
    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    #[Groups([RequestDTOBuilder::REQUEST_TYPE_FULL])]
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    #[Groups([RequestDTOBuilder::REQUEST_TYPE_FULL])]
    public function getGroupName(): GroupNameResponseDTO
    {
        return $this->groupName;
    }

    #[Groups([RequestDTOBuilder::REQUEST_TYPE_FULL])]
    public function getRoom(): RoomResponseDTO
    {
        return $this->room;
    }

    #[Groups([RequestDTOBuilder::REQUEST_TYPE_FULL])]
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    #[Groups([RequestDTOBuilder::REQUEST_TYPE_FULL])]
    public function getExternalIpAddress(): ?string
    {
        return $this->externalIpAddress;
    }

    #[
        ArrayShape([Devices::ROLE]),
        Groups([RequestDTOBuilder::REQUEST_TYPE_FULL])
    ]
    public function getRoles(): array
    {
        return $this->roles;
    }

    #[
        ArrayShape([StandardReadingTypeResponseInterface::class||[]]),
        Groups([RequestDTOBuilder::REQUEST_TYPE_FULL]),
    ]
    public function getSensorsData(): array
    {
        return $this->sensorsData;
    }
}
