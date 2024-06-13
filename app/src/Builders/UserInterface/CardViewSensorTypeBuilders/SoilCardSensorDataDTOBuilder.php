<?php

namespace App\Builders\UserInterface\CardViewSensorTypeBuilders;

use App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard\AnalogSensorCardViewDTOBuilder;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use JetBrains\PhpStorm\ArrayShape;

readonly class SoilCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    public function __construct(
        private AnalogSensorCardViewDTOBuilder $analogSensorBuilder,
        private SensorReadingTypeRepositoryFactory $sensorTypeRepositoryFactory,
    ) {
        parent::__construct($this->sensorTypeRepositoryFactory);
    }

    #[ArrayShape([StandardCardViewReadingResponseDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $analogSensorData = $this->analogSensorBuilder->buildAnalogSensorDataFromScalarArray($sensorData);

        return [
            $analogSensorData,
        ];
    }
}
