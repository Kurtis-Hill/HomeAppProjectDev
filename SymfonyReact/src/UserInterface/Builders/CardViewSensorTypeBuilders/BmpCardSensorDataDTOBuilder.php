<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\HumiditySensorCardViewDTOBuilder;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\LatitudeSensorCardViewDTOBuilder;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\TemperatureSensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\CardViewReadingResponseDTOInterface;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

class BmpCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    private TemperatureSensorCardViewDTOBuilder $tempSensorBuilder;

    private HumiditySensorCardViewDTOBuilder $humidSensorBuilder;

    private LatitudeSensorCardViewDTOBuilder $latSensorBuilder;

    public function __construct(
        TemperatureSensorCardViewDTOBuilder $tempSensorBuilder,
        HumiditySensorCardViewDTOBuilder $humiditySensorBuilder,
        LatitudeSensorCardViewDTOBuilder $latitudeSensorBuilder,
    ) {
        $this->tempSensorBuilder = $tempSensorBuilder;
        $this->humidSensorBuilder = $humiditySensorBuilder;
        $this->latSensorBuilder = $latitudeSensorBuilder;
    }

    #[ArrayShape([StandardCardViewReadingResponseDTO::class, StandardCardViewReadingResponseDTO::class, StandardCardViewReadingResponseDTO::class, CardViewReadingResponseDTOInterface::class])]
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
