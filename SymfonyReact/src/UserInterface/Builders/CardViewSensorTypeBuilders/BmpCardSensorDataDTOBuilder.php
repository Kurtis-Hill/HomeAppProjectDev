<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\Builders\CardViewReadingTypeBuilders\HumiditySensorCardViewBuilder;
use App\UserInterface\Builders\CardViewReadingTypeBuilders\LatitudeSensorCardViewBuilder;
use App\UserInterface\Builders\CardViewReadingTypeBuilders\TemperatureSensorCardViewBuilder;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;

class BmpCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    private TemperatureSensorCardViewBuilder $tempSensorBuilder;

    private HumiditySensorCardViewBuilder $humidSensorBuilder;

    private LatitudeSensorCardViewBuilder $latSensorBuilder;

    public function __construct(
        TemperatureSensorCardViewBuilder $tempSensorBuilder,
        HumiditySensorCardViewBuilder $humiditySensorBuilder,
        LatitudeSensorCardViewBuilder $latitudeSensorBuilder,
    ) {
        $this->tempSensorBuilder = $tempSensorBuilder;
        $this->humidSensorBuilder = $humiditySensorBuilder;
        $this->latSensorBuilder = $latitudeSensorBuilder;
    }

    #[ArrayShape([StandardCardViewDTO::class, StandardCardViewDTO::class, StandardCardViewDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->tempSensorBuilder->buildTemperatureSensorDataFromScalarArray($sensorData);
        $humidSensorData = $this->humidSensorBuilder->buildHumiditySensorDataFromScalarArray($sensorData);
        $latitudeSensorData = $this->latSensorBuilder->buildLatitudeSensorDataFromScalarArray($sensorData);

        return [
            $temperatureSensorData,
            $humidSensorData,
            $latitudeSensorData,
        ];
    }
}
