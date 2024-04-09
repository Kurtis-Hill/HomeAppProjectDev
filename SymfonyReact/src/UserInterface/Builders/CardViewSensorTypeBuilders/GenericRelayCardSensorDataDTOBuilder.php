<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Bool\RelaySensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
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
