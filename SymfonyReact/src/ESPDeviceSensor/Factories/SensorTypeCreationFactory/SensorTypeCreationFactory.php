<?php

namespace App\ESPDeviceSensor\Factories\SensorTypeCreationFactory;

use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders\BmpNewSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders\DallasNewSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders\DhtNewSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders\NewSensorReadingTypeBuilderInterface;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders\NewSensorReadingTypeBuilders\NewSensorReadingTypeBuilders\SoilNewSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;

class SensorTypeCreationFactory
{
    private BmpNewSensorReadingTypeBuilder $bmpSensorReadingTypeBuilder;

    private SoilNewSensorReadingTypeBuilder $soilSensorReadingTypeBuilder;

    private DhtNewSensorReadingTypeBuilder $dhtSensorReadingTypeBuilder;

    private DallasNewSensorReadingTypeBuilder $dallasSensorReadingTypeBuilder;

    public function __construct(
        BmpNewSensorReadingTypeBuilder $bmpSensorReadingTypeBuilder,
        SoilNewSensorReadingTypeBuilder $soilSensorReadingTypeBuilder,
        DallasNewSensorReadingTypeBuilder $dallasSensorReadingTypeBuilder,
        DhtNewSensorReadingTypeBuilder $dhtSensorReadingTypeBuilder,
    ) {
        $this->bmpSensorReadingTypeBuilder = $bmpSensorReadingTypeBuilder;
        $this->soilSensorReadingTypeBuilder = $soilSensorReadingTypeBuilder;
        $this->dhtSensorReadingTypeBuilder = $dhtSensorReadingTypeBuilder;
        $this->dallasSensorReadingTypeBuilder = $dallasSensorReadingTypeBuilder;
    }

    /**
     * @throws SensorTypeException
     */
    public function getSensorReadingTypeBuilder(string $sensorType): NewSensorReadingTypeBuilderInterface
    {
        return match ($sensorType) {
            Bmp::NAME => $this->bmpSensorReadingTypeBuilder,
            Soil::NAME => $this->soilSensorReadingTypeBuilder,
            Dallas::NAME => $this->dallasSensorReadingTypeBuilder,
            Dht::NAME => $this->dhtSensorReadingTypeBuilder,
            default => throw new SensorTypeException(
                sprintf(
                    SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED,
                    $sensorType
                )
            )
        };
    }
}
