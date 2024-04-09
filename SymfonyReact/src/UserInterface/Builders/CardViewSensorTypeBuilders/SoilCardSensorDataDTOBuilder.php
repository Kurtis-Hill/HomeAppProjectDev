<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\AnalogSensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
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
