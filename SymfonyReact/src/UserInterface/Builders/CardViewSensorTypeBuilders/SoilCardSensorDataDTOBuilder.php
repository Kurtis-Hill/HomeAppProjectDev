<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\DTO\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class SoilCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    #[Pure]
    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatCardSensorData(array $sensorData): array
    {
        $analogSensorData = $this->buildAnalogSensorData($sensorData);

        return [
            $analogSensorData,
        ];
    }
}
