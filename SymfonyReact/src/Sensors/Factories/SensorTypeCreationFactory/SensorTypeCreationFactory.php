<?php

namespace App\Sensors\Factories\SensorTypeCreationFactory;

use App\Sensors\Builders\SensorReadingTypeCreationBuilders\BmpNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\DallasNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\DhtNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\SoilNewReadingTypeBuilder;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeException;

class SensorTypeCreationFactory
{
    private BmpNewReadingTypeBuilder $bmpSensorReadingTypeBuilder;

    private SoilNewReadingTypeBuilder $soilSensorReadingTypeBuilder;

    private DhtNewReadingTypeBuilder $dhtSensorReadingTypeBuilder;

    private DallasNewReadingTypeBuilder $dallasSensorReadingTypeBuilder;

    public function __construct(
        BmpNewReadingTypeBuilder $bmpSensorReadingTypeBuilder,
        SoilNewReadingTypeBuilder $soilSensorReadingTypeBuilder,
        DallasNewReadingTypeBuilder $dallasSensorReadingTypeBuilder,
        DhtNewReadingTypeBuilder $dhtSensorReadingTypeBuilder,
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
