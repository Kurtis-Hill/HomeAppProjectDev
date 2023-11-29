<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\TemperatureSensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

class DallasSensorDataCardDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    private TemperatureSensorCardViewDTOBuilder $tempSensorBuilder;

    public function __construct(TemperatureSensorCardViewDTOBuilder $tempSensorBuilder)
    {
        $this->tempSensorBuilder = $tempSensorBuilder;
    }

    #[ArrayShape([StandardCardViewReadingResponseDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
//        dd($sensorData);
        $temperatureSensorData = $this->tempSensorBuilder->buildTemperatureSensorDataFromScalarArray($sensorData);

        return [
            $temperatureSensorData,
        ];
    }
}
