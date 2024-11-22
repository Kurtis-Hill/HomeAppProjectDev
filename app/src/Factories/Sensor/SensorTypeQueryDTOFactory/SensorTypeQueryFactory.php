<?php

namespace App\Factories\Sensor\SensorTypeQueryDTOFactory;

use App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders\BmpQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders\DallasQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders\DHTQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders\GenericMotionQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders\GenericRelayQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders\LdrQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders\SensorTypeQueryDTOBuilderInterface;
use App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders\ShtQueryTpeDTOBuilder;
use App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders\SoilQueryTypeDTOBuilder;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Exceptions\UserInterface\SensorTypeBuilderFailureException;

readonly class SensorTypeQueryFactory
{
    public function __construct(
        private DHTQueryTypeDTOBuilder $dhtQueryTypeDTOBuilder,
        private DallasQueryTypeDTOBuilder $dallasQueryTypeDTOBuilder,
        private SoilQueryTypeDTOBuilder $soilQueryTypeDTOBuilder,
        private BmpQueryTypeDTOBuilder $bmpQueryTypeDTOBuilder,
        private GenericRelayQueryTypeDTOBuilder $genericRelayQueryTypeDTOBuilder,
        private GenericMotionQueryTypeDTOBuilder $genericMotionQueryTypeDTOBuilder,
        private LdrQueryTypeDTOBuilder $ldrQueryTypeDTOBuilder,
        private ShtQueryTpeDTOBuilder $shtQueryTpeDTOBuilder
    ) {}

    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function getSensorTypeQueryDTOBuilder(string $sensorType): SensorTypeQueryDTOBuilderInterface
    {
        return match ($sensorType) {
            Dht::NAME => $this->dhtQueryTypeDTOBuilder,
            Dallas::NAME => $this->dallasQueryTypeDTOBuilder,
            Soil::NAME => $this->soilQueryTypeDTOBuilder,
            Bmp::NAME => $this->bmpQueryTypeDTOBuilder,
            GenericRelay::NAME => $this->genericRelayQueryTypeDTOBuilder,
            GenericMotion::NAME => $this->genericMotionQueryTypeDTOBuilder,
            LDR::NAME => $this->ldrQueryTypeDTOBuilder,
            Sht::NAME => $this->shtQueryTpeDTOBuilder,
            default => throw new SensorTypeBuilderFailureException(
                sprintf(
                    SensorTypeBuilderFailureException::SENSOR_TYPE_BUILDER_FAILURE_MESSAGE,
                    $sensorType
                )
            )
        };
    }
}
