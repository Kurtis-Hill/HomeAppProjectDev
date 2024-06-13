<?php

namespace App\Factories\UserInterface\SensorTypeCardDTOBuilderFactory;

use App\Builders\UserInterface\CardViewSensorTypeBuilders\BmpCardSensorDataDTOBuilder;
use App\Builders\UserInterface\CardViewSensorTypeBuilders\CardSensorDataDTOBuilderInterface;
use App\Builders\UserInterface\CardViewSensorTypeBuilders\DallasSensorDataCardDTOBuilder;
use App\Builders\UserInterface\CardViewSensorTypeBuilders\DhtCardSensorDataDTOBuilder;
use App\Builders\UserInterface\CardViewSensorTypeBuilders\GenericMotionCardSensorDataDTOBuilder;
use App\Builders\UserInterface\CardViewSensorTypeBuilders\GenericRelayCardSensorDataDTOBuilder;
use App\Builders\UserInterface\CardViewSensorTypeBuilders\LDRCardSensorDataDTOBuilder;
use App\Builders\UserInterface\CardViewSensorTypeBuilders\ShtCardSensorDataDTOBuilder;
use App\Builders\UserInterface\CardViewSensorTypeBuilders\SoilCardSensorDataDTOBuilder;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Exceptions\UserInterface\SensorTypeBuilderFailureException;

readonly class SensorTypeDTOBuilderFactory
{
    public function __construct(
        private BmpCardSensorDataDTOBuilder $bmpCardSensorDataDTOBuilder,
        private DallasSensorDataCardDTOBuilder $dallasSensorDataCardDTOBuilder,
        private DhtCardSensorDataDTOBuilder $dhtCardDTOBuilder,
        private SoilCardSensorDataDTOBuilder $soilCardSensorDataDTOBuilder,
        private GenericRelayCardSensorDataDTOBuilder $genericRelayCardSensorDataDTOBuilder,
        private GenericMotionCardSensorDataDTOBuilder $genericMotionCardSensorDataDTOBuilder,
        private LDRCardSensorDataDTOBuilder $ldrCardSensorDataDTOBuilder,
        private ShtCardSensorDataDTOBuilder $shtCardSensorDataDTOBuilder,
    ) {
    }

    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function getSensorDataDTOBuilderService(string $sensorType): CardSensorDataDTOBuilderInterface
    {
        return match ($sensorType) {
            Dht::NAME => $this->dhtCardDTOBuilder,
            Bmp::NAME => $this->bmpCardSensorDataDTOBuilder,
            Soil::NAME => $this->soilCardSensorDataDTOBuilder,
            Dallas::NAME => $this->dallasSensorDataCardDTOBuilder,
            GenericRelay::NAME => $this->genericRelayCardSensorDataDTOBuilder,
            GenericMotion::NAME => $this->genericMotionCardSensorDataDTOBuilder,
            LDR::NAME => $this->ldrCardSensorDataDTOBuilder,
            Sht::NAME => $this->shtCardSensorDataDTOBuilder,
            default => throw new SensorTypeBuilderFailureException(SensorTypeBuilderFailureException::SENSOR_TYPE_BUILDER_FAILURE_MESSAGE)
        };
    }
}
