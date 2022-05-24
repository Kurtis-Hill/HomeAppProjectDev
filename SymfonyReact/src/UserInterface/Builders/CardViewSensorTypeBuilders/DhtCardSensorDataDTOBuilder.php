<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\Builders\CardViewReadingTypeBuilders\HumiditySensorCardViewBuilder;
use App\UserInterface\Builders\CardViewReadingTypeBuilders\TemperatureSensorCardViewBuilder;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class DhtCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    private TemperatureSensorCardViewBuilder $tempSensorBuilder;

    private HumiditySensorCardViewBuilder $humidSensorBuilder;

    public function __construct(
        TemperatureSensorCardViewBuilder $tempSensorBuilder,
        HumiditySensorCardViewBuilder $humiditySensorBuilder
    ) {
        $this->tempSensorBuilder = $tempSensorBuilder;
        $this->humidSensorBuilder = $humiditySensorBuilder;
    }

    #[ArrayShape([StandardCardViewDTO::class, StandardCardViewDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->tempSensorBuilder->buildTemperatureSensorDataFromScalarArray($sensorData);
        $humiditySensorData = $this->humidSensorBuilder->buildHumiditySensorDataFromScalarArray($sensorData);

        return [
             $temperatureSensorData,
             $humiditySensorData
         ];
    }
}
