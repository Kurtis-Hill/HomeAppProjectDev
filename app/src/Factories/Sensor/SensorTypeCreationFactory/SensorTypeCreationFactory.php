<?php

namespace App\Factories\Sensor\SensorTypeCreationFactory;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\Bool\GenericMotionNewReadingTypeBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\Bool\GenericRelayNewReadingTypeBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\Standard\BmpNewReadingTypeBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\Standard\DallasNewReadingTypeBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\Standard\DhtNewReadingTypeBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\Standard\LDRNewReadingTypeBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\Standard\ShtNewReadingTypeBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\Standard\SoilNewReadingTypeBuilder;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Exceptions\Sensor\SensorTypeException;

readonly class SensorTypeCreationFactory
{
    public function __construct(
        private BmpNewReadingTypeBuilder $bmpSensorReadingTypeBuilder,
        private SoilNewReadingTypeBuilder $soilSensorReadingTypeBuilder,
        private DallasNewReadingTypeBuilder $dallasSensorReadingTypeBuilder,
        private DhtNewReadingTypeBuilder $dhtSensorReadingTypeBuilder,
        private GenericMotionNewReadingTypeBuilder $genericMotionSensorReadingTypeBuilder,
        private GenericRelayNewReadingTypeBuilder $genericRelaySensorReadingTypeBuilder,
        private LDRNewReadingTypeBuilder $ldrSensorReadingTypeBuilder,
        private ShtNewReadingTypeBuilder $shtSensorReadingTypeBuilder
    ) {
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
            GenericRelay::NAME => $this->genericRelaySensorReadingTypeBuilder,
            GenericMotion::NAME => $this->genericMotionSensorReadingTypeBuilder,
            LDR::NAME => $this->ldrSensorReadingTypeBuilder,
            Sht::NAME => $this->shtSensorReadingTypeBuilder,
            default => throw new SensorTypeException(
                sprintf(
                    SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED,
                    $sensorType
                )
            )
        };
    }
}
