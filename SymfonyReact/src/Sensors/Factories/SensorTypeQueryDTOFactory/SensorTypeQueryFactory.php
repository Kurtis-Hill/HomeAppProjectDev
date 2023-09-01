<?php

namespace App\Sensors\Factories\SensorTypeQueryDTOFactory;

use App\Sensors\Builders\SensorTypeQueryDTOBuilders\GenericMotionQueryTypeDTOBuilder;
use App\Sensors\Builders\SensorTypeQueryDTOBuilders\GenericRelayQueryTypeDTOBuilder;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Builders\SensorTypeQueryDTOBuilders\BmpQueryTypeDTOBuilder;
use App\Sensors\Builders\SensorTypeQueryDTOBuilders\SensorTypeQueryDTOBuilderInterface;
use App\Sensors\Builders\SensorTypeQueryDTOBuilders\DallasQueryTypeDTOBuilder;
use App\Sensors\Builders\SensorTypeQueryDTOBuilders\DHTQueryTypeDTOBuilder;
use App\Sensors\Builders\SensorTypeQueryDTOBuilders\SoilQueryTypeDTOBuilder;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

class SensorTypeQueryFactory
{
    private DHTQueryTypeDTOBuilder $dhtQueryTypeDTOBuilder;

    private DallasQueryTypeDTOBuilder $dallasQueryTypeDTOBuilder;

    private SoilQueryTypeDTOBuilder $soilQueryTypeDTOBuilder;

    private BmpQueryTypeDTOBuilder $bmpQueryTypeDTOBuilder;

    private GenericRelayQueryTypeDTOBuilder $genericRelayQueryTypeDTOBuilder;

    private GenericMotionQueryTypeDTOBuilder $genericMotionQueryTypeDTOBuilder;

    public function __construct(
        DHTQueryTypeDTOBuilder $dhtQueryTypeDTOBuilder,
        DallasQueryTypeDTOBuilder $dallasQueryTypeDTOBuilder,
        SoilQueryTypeDTOBuilder $soilQueryTypeDTOBuilder,
        BmpQueryTypeDTOBuilder $bmpQueryTypeDTOBuilder,
        GenericRelayQueryTypeDTOBuilder $genericRelayQueryTypeDTOBuilder,
        GenericMotionQueryTypeDTOBuilder $genericMotionQueryTypeDTOBuilder,
    ) {
        $this->dhtQueryTypeDTOBuilder = $dhtQueryTypeDTOBuilder;
        $this->dallasQueryTypeDTOBuilder = $dallasQueryTypeDTOBuilder;
        $this->soilQueryTypeDTOBuilder = $soilQueryTypeDTOBuilder;
        $this->bmpQueryTypeDTOBuilder = $bmpQueryTypeDTOBuilder;
        $this->genericRelayQueryTypeDTOBuilder = $genericRelayQueryTypeDTOBuilder;
        $this->genericMotionQueryTypeDTOBuilder = $genericMotionQueryTypeDTOBuilder;
    }

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
            default => throw new SensorTypeBuilderFailureException(
                sprintf(
                    SensorTypeBuilderFailureException::SENSOR_TYPE_BUILDER_FAILURE_MESSAGE,
                    $sensorType
                )
            )
        };
    }
}
