<?php

namespace App\Sensors\DTO\Response\SensorResponse;

use App\Common\Services\RequestTypeEnum;
use App\Devices\DTO\Response\DeviceResponseDTO;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeEncapsulationResponseDTO;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use App\User\DTO\Response\UserDTOs\UserResponseDTO;
use App\UserInterface\DTO\Response\CardView\CardViewResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class SensorResponseDTO
{
    public function __construct(
        private int $sensorID,
        private UserResponseDTO $createdBy,
        private string $sensorName,
        private DeviceResponseDTO $device,
        private SensorTypeResponseDTO $sensorType,
        private int $pinNumber,
        private ?SensorReadingTypeEncapsulationResponseDTO $sensorReadingTypes = null,
        private ?bool $canEdit = null,
        private ?bool $canDelete = null,
        private ?CardViewResponseDTO $cardView = null,
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensorID(): int
    {
        return $this->sensorID;
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
    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
    ])]
    public function getDevice(): DeviceResponseDTO
    {
        return $this->device;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
    ])]
    public function getSensorType(): SensorTypeResponseDTO
    {
        return $this->sensorType;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
    ])]
    public function getSensorReadingTypes(): SensorReadingTypeEncapsulationResponseDTO
    {
        return $this->sensorReadingTypes;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCanEdit(): ?bool
    {
        return $this->canEdit;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCanDelete(): ?bool
    {
        return $this->canDelete;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCardView(): ?CardViewResponseDTO
    {
        return $this->cardView;
    }

    #[Groups([
    RequestTypeEnum::FULL->value,
    RequestTypeEnum::ONLY->value,
    RequestTypeEnum::SENSITIVE_FULL->value,
    RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getPinNumber(): ?int
    {
        return $this->pinNumber;
    }
}
