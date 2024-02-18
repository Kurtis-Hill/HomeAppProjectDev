<?php

namespace App\UserInterface\Factories\SensorTypeCardDTOBuilderFactory;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\BmpCardSensorDataDTOBuilder;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\CardSensorDataDTOBuilderInterface;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\DallasSensorDataCardDTOBuilder;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\DhtCardSensorDataDTOBuilder;
use App\UserInterface\Builders\CardViewSensorTypeBuilders\SoilCardSensorDataDTOBuilder;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

class SensorTypeDTOBuilderFactory
{
    private BmpCardSensorDataDTOBuilder $bmpCardSensorDataDTOBuilder;

    private DallasSensorDataCardDTOBuilder $dallasSensorDataCardDTOBuilder;

    private DhtCardSensorDataDTOBuilder $dhtCardDTOBuilder;

    private SoilCardSensorDataDTOBuilder $soilCardDTOBuilder;

    public function __construct(
        BmpCardSensorDataDTOBuilder $bmpCardSensorDataDTOBuilder,
        DallasSensorDataCardDTOBuilder $dallasSensorDataCardDTOBuilder,
        DhtCardSensorDataDTOBuilder $dhtCardDTOBuilder,
        SoilCardSensorDataDTOBuilder $soilCardSensorDataDTOBuilder,
    ) {
        $this->bmpCardSensorDataDTOBuilder = $bmpCardSensorDataDTOBuilder;
        $this->dallasSensorDataCardDTOBuilder = $dallasSensorDataCardDTOBuilder;
        $this->dhtCardDTOBuilder = $dhtCardDTOBuilder;
        $this->soilCardDTOBuilder = $soilCardSensorDataDTOBuilder;
    }

    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function getSensorDataDTOBuilderService(string $sensorType): CardSensorDataDTOBuilderInterface
    {
        return match ($sensorType) {
            Dht::NAME => $this->dhtCardDTOBuilder,
            Bmp::NAME => $this->bmpCardSensorDataDTOBuilder,
            Soil::NAME => $this->soilCardDTOBuilder,
            Dallas::NAME => $this->dallasSensorDataCardDTOBuilder,
            default => throw new SensorTypeBuilderFailureException(SensorTypeBuilderFailureException::SENSOR_TYPE_BUILDER_FAILURE_MESSAGE)
        };
    }
}
