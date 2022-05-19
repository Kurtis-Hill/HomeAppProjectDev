<?php

namespace App\Sensors\Factories\SensorTypeCreationFactory;

use App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders\BmpNewSensorReadingTypeBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders\DallasNewSensorReadingTypeBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders\DhtNewSensorReadingTypeBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders\NewSensorReadingTypeBuilderInterface;
use App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders\SoilNewSensorReadingTypeBuilder;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeException;

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
