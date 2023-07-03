<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Bool\RelaySensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

class GenericRelayCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    public function __construct(private readonly RelaySensorCardViewDTOBuilder $relaySensorCardViewDTOBuilder) {}

    #[ArrayShape([BoolCardViewReadingResponseDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $relaySensorData = $this->relaySensorCardViewDTOBuilder->buildGenericRelaySensorDataFromScalarArray($sensorData);

        return [
            $relaySensorData
        ];
    }
}
