<?php

namespace App\Sensors\DTO\Response\Trigger;

use App\Common\DTO\Response\OperatorResponseDTO;
use App\Common\Services\RequestTypeEnum;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\Trigger\TriggerTypeResponse\TriggerTypeResponseDTO;
use App\Sensors\Entity\Sensor;
use App\User\DTO\Response\UserDTOs\UserResponseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class SensorTriggerResponseDTO
{
    public function __construct(
        private int $sensorTriggerID,
        private OperatorResponseDTO $operator,
        private TriggerTypeResponseDTO $triggerType,
        private bool|float $valueThatTriggers,
        private UserResponseDTO $createdBy,
        private ?string $startTime,
        private ?string $endTime,
        private string $createdAt,
        private string $updatedAt,
        private bool $monday,
        private bool $tuesday,
        private bool $wednesday,
        private bool $thursday,
        private bool $friday,
        private bool $saturday,
        private bool $sunday,
        private bool $override,
        private ?AllSensorReadingTypeResponseDTOInterface $baseReadingTypeThatTriggers,
        private ?AllSensorReadingTypeResponseDTOInterface $baseReadingTypeThatIsTriggered,
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensorTriggerID(): int
    {
        return $this->sensorTriggerID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getOperator(): OperatorResponseDTO
    {
        return $this->operator;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getTriggerType(): TriggerTypeResponseDTO
    {
        return $this->triggerType;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getValueThatTriggers(): bool|float
    {
        return $this->valueThatTriggers;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
//        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
//        RequestTypeEnum::SENSITIVE_ONLY->value,
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
    public function getStartTime(): ?string
    {
        return $this->startTime;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getEndTime(): ?string
    {
        return $this->endTime;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getBaseReadingTypeThatTriggers(): ?AllSensorReadingTypeResponseDTOInterface
    {
        return $this->baseReadingTypeThatTriggers;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getBaseReadingTypeThatIsTriggered(): ?AllSensorReadingTypeResponseDTOInterface
    {
        return $this->baseReadingTypeThatIsTriggered;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getOverride(): bool
    {
        return $this->override;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getMonday(): bool
    {
        return $this->monday;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getTuesday(): bool
    {
        return $this->tuesday;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getWednesday(): bool
    {
        return $this->wednesday;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getThursday(): bool
    {
        return $this->thursday;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getFriday(): bool
    {
        return $this->friday;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSaturday(): bool
    {
        return $this->saturday;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSunday(): bool
    {
        return $this->sunday;
    }
}
