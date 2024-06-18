<?php

namespace App\DTOs\Sensor\Response\Trigger\TriggerTypeResponse;

use App\DTOs\Operator\Response\OperatorResponseDTO;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\Bool\RelayResponseDTO;
use App\DTOs\Sensor\Response\SensorResponse\SensorResponseDTO;
use App\Services\Request\RequestTypeEnum;
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
