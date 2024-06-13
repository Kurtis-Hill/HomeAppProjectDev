<?php

namespace App\DTOs\Sensor\Response\Trigger;

use App\DTOs\Common\Response\DaysResponseDTO;
use App\DTOs\Operator\Response\OperatorResponseDTO;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\DTOs\Sensor\Response\Trigger\TriggerTypeResponse\TriggerTypeResponseDTO;
use App\DTOs\User\Response\UserDTOs\UserResponseDTO;
use App\Services\Request\RequestTypeEnum;
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
        private DaysResponseDTO $days,
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
    public function getDays(): DaysResponseDTO
    {
        return $this->days;
    }
}
