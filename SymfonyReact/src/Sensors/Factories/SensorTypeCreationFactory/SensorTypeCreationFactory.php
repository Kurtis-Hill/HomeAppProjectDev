<?php

namespace App\Sensors\Factories\SensorTypeCreationFactory;

use App\Sensors\Builders\SensorReadingTypeCreationBuilders\Bool\GenericMotionNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\Bool\GenericRelayNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\Standard\BmpNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\Standard\DallasNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\Standard\DhtNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\Standard\LDRNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\Standard\ShtNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\Standard\SoilNewReadingTypeBuilder;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeException;

class SensorTypeCreationFactory
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
    ) {}

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
