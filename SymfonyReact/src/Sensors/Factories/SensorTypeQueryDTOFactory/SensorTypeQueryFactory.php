<?php

namespace App\Sensors\Factories\SensorTypeQueryDTOFactory;

use App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders\BmpQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders\DallasQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders\DHTQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders\GenericMotionQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders\GenericRelayQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders\LdrQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders\SensorTypeQueryDTOBuilderInterface;
use App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders\ShtQueryTpeDTOBuilder;
use App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders\SoilQueryTypeDTOBuilder;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

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
