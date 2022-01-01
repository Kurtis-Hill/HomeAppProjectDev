<?php

namespace App\ESPDeviceSensor\Factories;

use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\BmpSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\DallasSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorTypeReadingObjectBuilders\DhtSensorReadingTypeObjectsBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorTypeReadingObjectBuilders\SensorReadingTypeObjectsBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SoilSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\ESPDeviceSensor\Exceptions\SensorTypeObjectBuilderException;

class SensorTypeObjectsBuilderFactory
{
    private DhtSensorReadingTypeObjectsBuilder $dhtSensorReadingTypeObjectsBuilder;

    private BmpSensorReadingTypeBuilder $bmpSensorReadingTypeBuilder;

    private SoilSensorReadingTypeBuilder $soilSensorReadingTypeBuilder;

    private DallasSensorReadingTypeBuilder $dallasSensorReadingTypeBuilder;

    public function __construct(
        DhtSensorReadingTypeObjectsBuilder $dhtSensorReadingTypeObjectsBuilder,
        BmpSensorReadingTypeBuilder $bmpSensorReadingTypeBuilder,
        SoilSensorReadingTypeBuilder $soilSensorReadingTypeBuilder,
        DallasSensorReadingTypeBuilder $dallasSensorReadingTypeBuilder,
    ) {
        $this->dhtSensorReadingTypeObjectsBuilder = $dhtSensorReadingTypeObjectsBuilder;
        $this->bmpSensorReadingTypeBuilder = $bmpSensorReadingTypeBuilder;
        $this->soilSensorReadingTypeBuilder = $soilSensorReadingTypeBuilder;
        $this->dallasSensorReadingTypeBuilder = $dallasSensorReadingTypeBuilder;
    }

    /**
     * @throws SensorTypeObjectBuilderException
     */
    public function getReadingTypeObjectBuilders(string $sensorType): SensorReadingTypeObjectsBuilder
    {
        return match ($sensorType) {
            Dht::NAME => $this->dhtSensorReadingTypeObjectsBuilder,
            Bmp::NAME => $this->bmpSensorReadingTypeBuilder,
            Soil::NAME => $this->soilSensorReadingTypeBuilder,
            Dallas::NAME => $this->dallasSensorReadingTypeBuilder,
            default => throw new SensorTypeObjectBuilderException(SensorTypeObjectBuilderException::MESSAGE)
        };
    }
}
