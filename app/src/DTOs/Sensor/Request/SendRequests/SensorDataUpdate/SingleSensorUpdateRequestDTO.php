<?php

namespace App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate;

use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class SingleSensorUpdateRequestDTO implements SensorUpdateRequestDTOInterface
{
    public function __construct(
        private string $sensorName,
        private int $pinNumber,
        private int $readingInterval,
    ) {}

    #[Groups([
        GenericRelay::NAME,
        Dht::NAME,
        Dallas::NAME,
        Soil::NAME,
        GenericMotion::NAME,
        LDR::NAME,
        Sht::NAME,
    ])]
    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    #[Groups([
        GenericRelay::NAME,
        Dht::NAME,
        Dallas::NAME,
        Soil::NAME,
        GenericMotion::NAME,
        LDR::NAME,
        Sht::NAME,
    ])]
    public function getPinNumber(): int
    {
        return $this->pinNumber;
    }

    #[Groups([
        GenericRelay::NAME,
        Dht::NAME,
        Dallas::NAME,
        Soil::NAME,
        GenericMotion::NAME,
        LDR::NAME,
        Sht::NAME,
    ])]
    public function getReadingInterval(): int
    {
        return $this->readingInterval;
    }
}
