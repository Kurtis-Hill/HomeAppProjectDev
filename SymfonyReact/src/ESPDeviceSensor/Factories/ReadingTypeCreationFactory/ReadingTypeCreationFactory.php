<?php

namespace App\ESPDeviceSensor\Factories\ReadingTypeCreationFactory;

use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\BmpSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\DallasSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\DhtSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorReadingTypeBuilderInterface;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SoilSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;

class ReadingTypeCreationFactory
{
    private BmpSensorReadingTypeBuilder $bmpSensorReadingTypeBuilder;

    private SoilSensorReadingTypeBuilder $soilSensorReadingTypeBuilder;

    private DhtSensorReadingTypeBuilder $dhtSensorReadingTypeBuilder;

    private DallasSensorReadingTypeBuilder $dallasSensorReadingTypeBuilder;

    public function __construct(
        BmpSensorReadingTypeBuilder $bmpSensorReadingTypeBuilder,
        SoilSensorReadingTypeBuilder $soilSensorReadingTypeBuilder,
        DallasSensorReadingTypeBuilder $dallasSensorReadingTypeBuilder,
        DhtSensorReadingTypeBuilder $dhtSensorReadingTypeBuilder,
    ) {
        $this->bmpSensorReadingTypeBuilder = $bmpSensorReadingTypeBuilder;
        $this->soilSensorReadingTypeBuilder = $soilSensorReadingTypeBuilder;
        $this->dhtSensorReadingTypeBuilder = $dhtSensorReadingTypeBuilder;
        $this->dallasSensorReadingTypeBuilder = $dallasSensorReadingTypeBuilder;
    }

    /**
     * @throws SensorTypeException
     */
    public function getSensorReadingTypeBuilder(string $sensorType): SensorReadingTypeBuilderInterface
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
