<?php

namespace App\Sensors\DTO\Response\TriggerTypeResponse;

use App\Common\DTO\Response\OperatorResponseDTO;
use App\Common\Services\RequestTypeEnum;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Bool\RelayResponseDTO;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class TriggerFormEncapsulationDTO
{
    public function __construct(
        #[ArrayShape([OperatorResponseDTO::class])]
        private array $operators,
        #[ArrayShape([TriggerTypeResponseDTO::class])]
        private array $triggerTypes,
        #[ArrayShape([RelayResponseDTO::class])]
        private array $relays,
        #[ArrayShape([SensorResponseDTO::class])]
        private array $sensors,
    ) {
    }

    #[
        ArrayShape([OperatorResponseDTO::class]),
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::ONLY->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getOperators(): array
    {
        return $this->operators;
    }

    #[
        ArrayShape([TriggerTypeResponseDTO::class]),
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::ONLY->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getTriggerTypes(): array
    {
        return $this->triggerTypes;
    }

    #[
        ArrayShape([RelayResponseDTO::class]),
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::ONLY->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getRelays(): array
    {
        return $this->relays;
    }

    #[
        ArrayShape([SensorResponseDTO::class]),
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::ONLY->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getSensors(): array
    {
        return $this->sensors;
    }
}
