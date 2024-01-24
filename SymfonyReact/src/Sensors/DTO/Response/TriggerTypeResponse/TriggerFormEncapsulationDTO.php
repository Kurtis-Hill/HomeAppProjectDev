<?php

namespace App\Sensors\DTO\Response\TriggerTypeResponse;

use App\Common\DTO\Response\OperatorResponseDTO;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Bool\RelayResponseDTO;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

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

    #[ArrayShape([OperatorResponseDTO::class])]
    public function getOperatorDTOs(): array
    {
        return $this->operatorDTOs;
    }

    #[ArrayShape([TriggerTypeResponseDTO::class])]
    public function getTriggerTypeDTOs(): array
    {
        return $this->triggerTypeDTOs;
    }

    #[ArrayShape([RelayResponseDTO::class])]
    public function getRelayDTOs(): array
    {
        return $this->relayDTOs;
    }

    #[ArrayShape([SensorResponseDTO::class])]
    public function getSensorDTOs(): array
    {
        return $this->sensorDTOs;
    }
}
