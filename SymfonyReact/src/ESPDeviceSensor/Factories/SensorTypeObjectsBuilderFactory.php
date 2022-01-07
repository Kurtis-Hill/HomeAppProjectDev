<?php

namespace App\ESPDeviceSensor\Factories;

use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\BmpNewSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\DallasNewSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorTypeReadingObjectBuilders\BmpSensorReadingTypeObjectsBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorTypeReadingObjectBuilders\DallasSensorReadingTypeObjectsBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorTypeReadingObjectBuilders\DhtSensorReadingTypeObjectsBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorTypeReadingObjectBuilders\SensorReadingTypeObjectsBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorTypeReadingObjectBuilders\SoilSensorReadingTypeObjectsBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SoilNewSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\ESPDeviceSensor\Exceptions\SensorTypeObjectBuilderException;

//@TODO rename to SensorReadingTypeObjectsBuidler same as these builders
class SensorTypeObjectsBuilderFactory
{
    private DhtSensorReadingTypeObjectsBuilder $dhtSensorReadingTypeObjectsBuilder;

    private BmpSensorReadingTypeObjectsBuilder $bmpSensorReadingTypeBuilder;

    private SoilSensorReadingTypeObjectsBuilder $soilSensorReadingTypeBuilder;

    private DallasSensorReadingTypeObjectsBuilder $dallasSensorReadingTypeBuilder;
    public function __construct(
        DhtSensorReadingTypeObjectsBuilder $dhtSensorReadingTypeObjectsBuilder,
        BmpSensorReadingTypeObjectsBuilder $bmpSensorReadingTypeBuilder,
        SoilSensorReadingTypeObjectsBuilder $soilSensorReadingTypeBuilder,
        DallasSensorReadingTypeObjectsBuilder $dallasSensorReadingTypeBuilder,
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
