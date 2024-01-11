<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\TemperatureSensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
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
