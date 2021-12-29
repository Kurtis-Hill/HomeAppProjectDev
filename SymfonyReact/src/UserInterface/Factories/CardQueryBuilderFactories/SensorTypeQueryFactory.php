<?php

namespace App\UserInterface\Factories\CardQueryBuilderFactories;

use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder\BmpQueryTypeDTOBuilder;
use App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder\CardSensorTypeQueryDTOBuilder;
use App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder\DallasQueryTypeDTOBuilder;
use App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder\DHTQueryTypeDTOBuilder;
use App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder\SoilQueryTypeDTOBuilder;

class SensorTypeQueryFactory
{
    private DHTQueryTypeDTOBuilder $dhtQueryTypeDTOBuilder;

    private DallasQueryTypeDTOBuilder $dallasQueryTypeDTOBuilder;

    private SoilQueryTypeDTOBuilder $soilQueryTypeDTOBuilder;

    private BmpQueryTypeDTOBuilder $bmpQueryTypeDTOBuilder;

    public function __construct(
        DHTQueryTypeDTOBuilder $dhtQueryTypeDTOBuilder,
        DallasQueryTypeDTOBuilder $dallasQueryTypeDTOBuilder,
        SoilQueryTypeDTOBuilder $soilQueryTypeDTOBuilder,
        BmpQueryTypeDTOBuilder $bmpQueryTypeDTOBuilder,
    ) {
        $this->dhtQueryTypeDTOBuilder = $dhtQueryTypeDTOBuilder;
        $this->dallasQueryTypeDTOBuilder = $dallasQueryTypeDTOBuilder;
        $this->soilQueryTypeDTOBuilder = $soilQueryTypeDTOBuilder;
        $this->bmpQueryTypeDTOBuilder = $bmpQueryTypeDTOBuilder;
    }

    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function getSensorTypeQueryDTOBuilder(string $sensorType): CardSensorTypeQueryDTOBuilder
    {
        return match ($sensorType) {
            Dht::NAME => $this->dhtQueryTypeDTOBuilder,
            Dallas::NAME => $this->dallasQueryTypeDTOBuilder,
            Soil::NAME => $this->soilQueryTypeDTOBuilder,
            Bmp::NAME => $this->bmpQueryTypeDTOBuilder,
            default => throw new SensorTypeBuilderFailureException(
                sprintf(
                    SensorTypeBuilderFailureException::SENSOR_TYPE_BUILDER_FAILURE_MESSAGE,
                    $sensorType
                )
            )
        };
    }
}
