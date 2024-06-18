<?php
declare(strict_types=1);

namespace App\DTOs\Device\Response;

use App\DTOs\Sensor\Response\SensorResponse\SensorResponseDTO;
use App\DTOs\User\Response\GroupDTOs\GroupResponseDTO;
use App\DTOs\User\Response\RoomDTOs\RoomResponseDTO;
use App\DTOs\User\Response\UserDTOs\UserResponseDTO;
use App\Entity\Device\Devices;
use App\Services\Request\RequestTypeEnum;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class DeviceResponseDTO
{
    public function __construct(
        private int $deviceID,
        private string $deviceName,
        private ?string $secret,
        private GroupResponseDTO $group,
        private RoomResponseDTO $room,
        private ?string $ipAddress,
        private ?string $externalIpAddress,
        private array $roles,
        private array $sensorData,
        private UserResponseDTO $createdBy,
        private ?bool $canEdit = null,
        private ?bool $canDelete = null,
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getDeviceID(): int
    {
        return $this->deviceID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    #[Groups([
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
    ])]
    public function getGroup(): GroupResponseDTO
    {
        return $this->group;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
    ])]
    public function getRoom(): RoomResponseDTO
    {
        return $this->room;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getExternalIpAddress(): ?string
    {
        return $this->externalIpAddress;
    }

    #[
        ArrayShape([Devices::ROLE]),
        Groups([
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getRoles(): array
    {
        return $this->roles;
    }

    #[
        ArrayShape([SensorResponseDTO::class||[]]),
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
        ]),
    ]
    public function getSensorData(): array
    {
        return $this->sensorData;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
    ])]
    public function getCreatedBy(): UserResponseDTO
    {
        return $this->createdBy;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCanEdit(): bool
    {
        return $this->canEdit;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCanDelete(): bool
    {
        return $this->canDelete;
    }
}
