<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\HumiditySensorCardViewDTOBuilder;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\LatitudeSensorCardViewDTOBuilder;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\TemperatureSensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\CardViewReadingResponseDTOInterface;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

readonly class BmpCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    public function __construct(
        private TemperatureSensorCardViewDTOBuilder $tempSensorBuilder,
        private HumiditySensorCardViewDTOBuilder $humiditySensorBuilder,
        private LatitudeSensorCardViewDTOBuilder $latitudeSensorBuilder,
        private SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory,
    ) {
        parent::__construct($this->sensorReadingTypeRepositoryFactory);
    }

    #[ArrayShape([StandardCardViewReadingResponseDTO::class, StandardCardViewReadingResponseDTO::class, StandardCardViewReadingResponseDTO::class, CardViewReadingResponseDTOInterface::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->tempSensorBuilder->buildTemperatureSensorDataFromScalarArray($sensorData);
        $humidSensorData = $this->humiditySensorBuilder->buildHumiditySensorDataFromScalarArray($sensorData);
        $latitudeSensorData = $this->latitudeSensorBuilder->buildLatitudeSensorDataFromScalarArray($sensorData);

        return [
            $temperatureSensorData,
            $humidSensorData,
            $latitudeSensorData,
        ];
    }
}
