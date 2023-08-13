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
                Dht::NAME => RegularSensorUpdateRequestDTO::class,
                Bmp::NAME => RegularSensorUpdateRequestDTO::class,
                GenericMotion::NAME => RegularSensorUpdateRequestDTO::class,
                GenericRelay::NAME => RegularSensorUpdateRequestDTO::class,
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
