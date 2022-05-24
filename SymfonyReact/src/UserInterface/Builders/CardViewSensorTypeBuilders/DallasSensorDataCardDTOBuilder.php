<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\Builders\CardViewReadingTypeBuilders\TemperatureSensorCardViewBuilder;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;

class DallasSensorDataCardDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    private TemperatureSensorCardViewBuilder $tempSensorBuilder;

    public function __construct(TemperatureSensorCardViewBuilder $tempSensorBuilder)
    {
        $this->tempSensorBuilder = $tempSensorBuilder;
    }

    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->tempSensorBuilder->buildTemperatureSensorDataFromScalarArray($sensorData);

        return [
            $temperatureSensorData,
        ];
    }
}
