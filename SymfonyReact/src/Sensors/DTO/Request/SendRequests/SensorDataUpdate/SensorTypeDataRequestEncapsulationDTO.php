<?php

namespace App\Sensors\DTO\Request\SendRequests\SensorDataUpdate;

use App\Devices\DTO\Request\DeviceRequest\DeviceRequestDTOInterface;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class SensorTypeDataRequestEncapsulationDTO implements DeviceRequestDTOInterface
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
}
