<?php

namespace App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate;

use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class SensorTypeDataRequestEncapsulationDTO
{
    public function __construct(
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        private ?array $relay = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        private ?array $dht = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        private ?array $dallas = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        private ?array $soil = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        private ?array $motion = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        private ?array $bmp = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        private ?array $ldr = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        private ?array $sht = null,
    ) {}

    #[
        ArrayShape([SingleSensorUpdateRequestDTO::class]),
        Groups([GenericRelay::NAME]),
    ]
    public function getRelay(): ?array
    {
        return $this->relay;
    }

    #[
        ArrayShape([SingleSensorUpdateRequestDTO::class]),
        Groups([Dht::NAME]),
    ]
    public function getDht(): ?array
    {
        return $this->dht;
    }

    #[
        ArrayShape([SingleSensorUpdateRequestDTO::class]),
        Groups([Dallas::NAME]),
    ]
    public function getDallas(): ?array
    {
        return $this->dallas;
    }

    #[
        ArrayShape([SingleSensorUpdateRequestDTO::class]),
        Groups([Soil::NAME]),
    ]
    public function getSoil(): ?array
    {
        return $this->soil;
    }

    #[
        ArrayShape([SingleSensorUpdateRequestDTO::class]),
        Groups([GenericMotion::NAME]),
    ]
    public function getMotion(): ?array
    {
        return $this->motion;
    }

    #[
        ArrayShape([SingleSensorUpdateRequestDTO::class]),
        Groups([Bmp::NAME]),
    ]
    public function getBmp(): ?array
    {
        return $this->bmp;
    }

    #[
        ArrayShape([SingleSensorUpdateRequestDTO::class]),
        Groups([Ldr::NAME]),
    ]
    public function getLdr(): ?array
    {
        return $this->ldr;
    }

    #[
        ArrayShape([SingleSensorUpdateRequestDTO::class]),
        Groups([Sht::NAME]),
    ]
    public function getSht(): ?array
    {
        return $this->sht;
    }
}
