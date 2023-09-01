<?php

namespace App\Sensors\DTO\Request\SendRequests\SensorDataUpdate;

use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
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
    ])]
    public function getReadingInterval(): int
    {
        return $this->readingInterval;
    }
}
