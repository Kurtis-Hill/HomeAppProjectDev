<?php

namespace App\Sensors\Factories\SensorUpdateRequestFactory;

use App\Sensors\Builders\SensorUpdateRequestDTOBuilder\BusSensorUpdateRequestDTOBuilder;
use App\Sensors\Builders\SensorUpdateRequestDTOBuilder\RegularSensorUpdateRequestDTOBuilder;
use App\Sensors\Builders\SensorUpdateRequestDTOBuilder\SensorUpdateRequestDTOBuilderInterface;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeNotFoundException;

readonly class SensorUpdateRequestBuilderFactory
{
    public function __construct(
        private RegularSensorUpdateRequestDTOBuilder $regularSensorUpdateRequestDTOBuilder,
        private BusSensorUpdateRequestDTOBuilder $busSensorUpdateRequestDTOBuilder,
    ) {}

    /**
     * @throws SensorTypeNotFoundException
     */
    public function getSensorUpdateRequestBuilder(string $sensorType): SensorUpdateRequestDTOBuilderInterface
    {
        return match ($sensorType) {
            Dht::NAME,
            GenericRelay::NAME,
            GenericMotion::NAME,
            Bmp::NAME => $this->regularSensorUpdateRequestDTOBuilder,
            Dallas::NAME, Soil::NAME => $this->busSensorUpdateRequestDTOBuilder,
            default => throw new SensorTypeNotFoundException(),
        };
    }
}
