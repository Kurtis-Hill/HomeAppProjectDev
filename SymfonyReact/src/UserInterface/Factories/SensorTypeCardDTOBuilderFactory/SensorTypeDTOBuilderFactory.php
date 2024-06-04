<?php

namespace App\UserInterface\Factories\SensorTypeCardDTOBuilderFactory;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\BmpCardSensorDataDTOBuilder;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\CardSensorDataDTOBuilderInterface;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\DallasSensorDataCardDTOBuilder;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\DhtCardSensorDataDTOBuilder;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\GenericMotionCardSensorDataDTOBuilder;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\GenericRelayCardSensorDataDTOBuilder;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\LDRCardSensorDataDTOBuilder;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\ShtCardSensorDataDTOBuilder;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\SoilCardSensorDataDTOBuilder;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

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
