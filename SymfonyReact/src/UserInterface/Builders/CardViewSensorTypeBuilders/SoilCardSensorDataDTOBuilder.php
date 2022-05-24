<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class SoilCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    #[Pure]
    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $analogSensorData = $this->buildAnalogSensorData($sensorData);

        return [
            $analogSensorData,
        ];
    }
}
