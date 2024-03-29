<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\AnalogSensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

class LDRCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    private AnalogSensorCardViewDTOBuilder $analogSensorBuilder;

    public function __construct(AnalogSensorCardViewDTOBuilder $analogSensorBuilder)
    {
        $this->analogSensorBuilder = $analogSensorBuilder;
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
