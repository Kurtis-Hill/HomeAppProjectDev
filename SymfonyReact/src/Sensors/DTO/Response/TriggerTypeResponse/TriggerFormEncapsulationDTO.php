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
        private array $operatorDTOs,
        #[ArrayShape([TriggerTypeResponseDTO::class])]
        private array $triggerTypeDTOs,
        #[ArrayShape([RelayResponseDTO::class])]
        private array $relayDTOs,
        #[ArrayShape([SensorResponseDTO::class])]
        private array $sensorDTOs,
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
    public function getOperatorDTOs(): array
    {
        return $this->operatorDTOs;
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
    public function getTriggerTypeDTOs(): array
    {
        return $this->triggerTypeDTOs;
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
    public function getRelayDTOs(): array
    {
        return $this->relayDTOs;
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
    public function getSensorDTOs(): array
    {
        return $this->sensorDTOs;
    }
}
