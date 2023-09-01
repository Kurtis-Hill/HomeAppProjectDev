<?php

namespace App\Sensors\DTO\Request\SendRequests\SensorDataUpdate;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
use JetBrains\PhpStorm\ArrayShape;

readonly class SensorUpdateDataEncapsulationDTO implements SensorUpdateEncapsulationInterface
{
    public function __construct(
        #[ArrayShape(
            [
                Dht::NAME => SingleSensorUpdateRequestDTO::class,
                Bmp::NAME => SingleSensorUpdateRequestDTO::class,
                GenericMotion::NAME => SingleSensorUpdateRequestDTO::class,
                GenericRelay::NAME => SingleSensorUpdateRequestDTO::class,
                Dallas::NAME => BusSensorUpdateRequestDTO::class,
                Soil::NAME => BusSensorUpdateRequestDTO::class,
            ]
        )]
        private array $sensorData = [],
    ) {}

    public function getSensorData(): array
    {
        return $this->sensorData;
    }
}
