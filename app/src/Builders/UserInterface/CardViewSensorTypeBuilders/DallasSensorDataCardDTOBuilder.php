<?php

namespace App\Builders\UserInterface\CardViewSensorTypeBuilders;

use App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard\TemperatureSensorCardViewDTOBuilder;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use JetBrains\PhpStorm\ArrayShape;

readonly class DallasSensorDataCardDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    public function __construct(
        private TemperatureSensorCardViewDTOBuilder $tempSensorBuilder,
        private SensorReadingTypeRepositoryFactory $sensorTypeRepositoryFactory,
    ) {
        parent::__construct($this->sensorTypeRepositoryFactory);
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
