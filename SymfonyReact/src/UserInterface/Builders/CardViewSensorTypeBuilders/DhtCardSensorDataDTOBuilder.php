<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\HumiditySensorCardViewDTOBuilder;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\TemperatureSensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

class DhtCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    private TemperatureSensorCardViewDTOBuilder $tempSensorBuilder;

    private HumiditySensorCardViewDTOBuilder $humidSensorBuilder;

    public function __construct(
        TemperatureSensorCardViewDTOBuilder $tempSensorBuilder,
        HumiditySensorCardViewDTOBuilder $humiditySensorBuilder
    ) {
        $this->tempSensorBuilder = $tempSensorBuilder;
        $this->humidSensorBuilder = $humiditySensorBuilder;
    }

    #[ArrayShape([StandardCardViewReadingResponseDTO::class, StandardCardViewReadingResponseDTO::class])]
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
