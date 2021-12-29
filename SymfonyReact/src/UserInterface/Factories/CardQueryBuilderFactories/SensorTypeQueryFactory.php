<?php

namespace App\UserInterface\Factories\CardQueryBuilderFactories;

use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder\CardSensorTypeQueryDTOBuilder;
use App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder\DallasQueryTypeDTOBuilder;
use App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder\DHTQueryTypeDTOBuilder;
use App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder\SoilQueryTypeDTOBuilder;

class SensorTypeQueryFactory
{
    private DHTQueryTypeDTOBuilder $dhtQueryTypeDTOBuilder;

    private DallasQueryTypeDTOBuilder $dallasQueryTypeDTOBuilder;

    private SoilQueryTypeDTOBuilder $soilQueryTypeDTOBuilder;

    public function __construct(
        DHTQueryTypeDTOBuilder $dhtQueryTypeDTOBuilder,
        DallasQueryTypeDTOBuilder $dallasQueryTypeDTOBuilder,
        SoilQueryTypeDTOBuilder $soilQueryTypeDTOBuilder,
    ) {
        $this->dhtQueryTypeDTOBuilder = $dhtQueryTypeDTOBuilder;
        $this->dallasQueryTypeDTOBuilder = $dallasQueryTypeDTOBuilder;
        $this->soilQueryTypeDTOBuilder = $soilQueryTypeDTOBuilder;
    }

    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function getSensorTypeQueryDTOBuilder(string $sensorType): CardSensorTypeQueryDTOBuilder
    {
        return match ($sensorType) {
            'dht' => $this->dhtQueryTypeDTOBuilder,
            'dallas' => $this->dallasQueryTypeDTOBuilder,
            'soil' => $this->soilQueryTypeDTOBuilder,
            default => throw new SensorTypeBuilderFailureException(
                sprintf(
                    SensorTypeBuilderFailureException::SENSOR_TYPE_BUILDER_FAILURE_MESSAGE,
                    $sensorType
                )
            )
        };
    }
}
