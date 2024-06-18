<?php

namespace App\Builders\UserInterface\CardViewSensorTypeBuilders;

use App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Bool\RelaySensorCardViewDTOBuilder;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use JetBrains\PhpStorm\ArrayShape;

readonly class GenericRelayCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    public function __construct(
        private RelaySensorCardViewDTOBuilder $relaySensorCardViewDTOBuilder,
        private SensorReadingTypeRepositoryFactory $sensorTypeRepositoryFactory,
    ) {
        parent::__construct($this->sensorTypeRepositoryFactory);
    }

    #[ArrayShape([BoolCardViewReadingResponseDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $relaySensorData = $this->relaySensorCardViewDTOBuilder->buildGenericRelaySensorDataFromScalarArray($sensorData);

        return [
            $relaySensorData
        ];
    }
}
