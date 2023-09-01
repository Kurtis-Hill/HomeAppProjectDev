<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Bool\MotionSensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

class GenericMotionCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    public function __construct(private readonly MotionSensorCardViewDTOBuilder $motionSensorCardViewDTOBuilder) {}

    #[ArrayShape([BoolCardViewReadingResponseDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $motionSensorData = $this->motionSensorCardViewDTOBuilder->buildGenericMotionSensorDataFromScalarArray($sensorData);

        return [
            $motionSensorData
        ];
    }
}
