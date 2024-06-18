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

readonly class SensorUpdateDataEncapsulationDTO implements SensorUpdateEncapsulationInterface
{
    public function __construct(
        #[ArrayShape(
            [
                Dht::NAME => SingleSensorUpdateRequestDTO::class,
                Bmp::NAME => SingleSensorUpdateRequestDTO::class,
                GenericMotion::NAME => SingleSensorUpdateRequestDTO::class,
                GenericRelay::NAME => SingleSensorUpdateRequestDTO::class,
                Dallas::NAME => SingleSensorUpdateRequestDTO::class,
                Soil::NAME => SingleSensorUpdateRequestDTO::class,
                LDR::NAME => SingleSensorUpdateRequestDTO::class,
                Sht::NAME => SingleSensorUpdateRequestDTO::class,
            ]
        )]
        private array $sensorData = [],
    ) {}

    public function getSensorData(): array
    {
        return $this->sensorData;
    }
}
