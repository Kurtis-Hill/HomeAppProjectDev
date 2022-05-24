<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\Builders\CardViewReadingTypeBuilders\AnalogSensorCardViewBuilder;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;

class SoilCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    private AnalogSensorCardViewBuilder $analogSensorBuilder;

    public function __construct(AnalogSensorCardViewBuilder $analogSensorBuilder)
    {
        $this->analogSensorBuilder = $analogSensorBuilder;
    }

    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $analogSensorData = $this->analogSensorBuilder->buildAnalogSensorDataFromScalarArray($sensorData);

        return [
            $analogSensorData,
        ];
    }
}
