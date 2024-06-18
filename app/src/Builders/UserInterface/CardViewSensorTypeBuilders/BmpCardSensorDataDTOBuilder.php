<?php

namespace App\Builders\UserInterface\CardViewSensorTypeBuilders;

use App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard\HumiditySensorCardViewDTOBuilder;
use App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard\LatitudeSensorCardViewDTOBuilder;
use App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard\TemperatureSensorCardViewDTOBuilder;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\CardViewReadingResponseDTOInterface;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
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
